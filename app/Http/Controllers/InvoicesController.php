<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceCreatedMail;
use Illuminate\Support\Facades\Cache;
use App\Model\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Model\Property;
use App\User;
use Carbon\Carbon;

class InvoicesController extends Controller
{
    /**
     * Create a new OrdersController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $invoice = Invoice::with(['property', 'user'])->get();
            return $this->sendResponse($invoice, 'Invoices fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), Invoice::$createInvoiceRules);
            if ($validator->fails()) {
                return $this->sendError('validation error', $validator->errors(), 422);
            }
            $property_id = $request->input('property_id');
            $fractions_qty = $request->input('fractions_qty');
            $property = Property::find($property_id);
            //check if doesnt property exists
            if (!$property) {
                return $this->sendError('Property not found', null, 404);
            }

            //check if fraction qty is ideal
            if ($property->investment_population < $fractions_qty) {
                return $this->sendError('Fractions requested is not available', null, 409);
            }

            //check order
            $auth_user = User::find(Auth::id());
            $check_order = $auth_user->orders()->get();

            //check if fractions are more than 200
            $fractions_total = array_reduce(array_map(function ($order) {
                return ($order['fractions_qty']);
            }, $check_order->where('property_id', $property_id)->toArray()), function ($acc, $val) {
                return $val + $acc;
            }, 0);

            if ($fractions_total + $fractions_qty > 200) {
                return $this->sendError("You can't purchase more than 200 fractions of the same property", null, 409);
            };

            Cache::forget('invoice:' . $auth_user->id);

            //validate yield period
            if (!in_array($request->input('yield_period'), $property->holding_period)) {
                return $this->sendError("Yield period not allowed", null, 409);
            }

            $invoice = new Invoice;
            $invoice->property_id = $request->input('property_id');
            $invoice->user_id = Auth::id();
            $invoice->invoice_number = rand(1000000, 9000000);
            $invoice->fractions_qty = $request->input('fractions_qty');
            $invoice->yield_period = $request->input('yield_period');
            $invoice->price = $request->input('price');
            $invoice->due_date = Carbon::now()->addDays(3);
            $invoice->save();

            // Send email
            $user =  Auth::user();
            $props = $invoice->property()->get();

            $data = [
                'property_name' => $props[0]->name,
                'fraction_qty' => $invoice->fractions_qty,
                'invoice_number' =>  $invoice->invoice_number,
                'price' => $this->formatMoney($invoice->price),
                'due_date' => $invoice->due_date->toDayDateTimeString(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
            ];

            Mail::to($user->email, $user->firstname)->send(new InvoiceCreatedMail($data));

            return $this->sendResponse($invoice, 'Invoice created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Oops!, Something went wrong', $e->getMessage(), 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::with(['property', 'user'])->find($id);
            if (!$invoice) {
                return $this->sendError('Invoice not found', null, 404);
            }
            return $this->sendResponse($invoice, 'Invoice fetched successfully', 200);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $invoice = Invoice::find($id);
            if (!$invoice) {
                return $this->sendError('Invoice not found', null, 404);
            }
            $invoice->delete();
            return $this->sendResponse(null, 'Invoice deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
