<?php

namespace App\Http\Controllers;

use App\Mail\OrderSuccessMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Model\Order;
use App\Model\Property;

class OrdersController extends Controller
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
            $order = Order::with(['property', 'user'])->get();
            return $this->sendResponse($order, 'Orders fetched successfully');
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
            $validator = Validator::make($request->all(), Order::$createOrderRules);
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
                return $this->sendError('Fractions requested in not available', null, 409);
            }

            $order = new Order;
            $order->property_id = $request->input('property_id');
            $order->user_id = Auth::id();
            $order->fractions_qty = $request->input('fractions_qty');
            $order->price = $request->input('price');
            $order->end_date = Carbon::now()->addYear($property->holding_period);
            $order->save();

            //reduce the property fraction qty
            $property->investment_population -= $fractions_qty;
            $property->update();

            //Send email
            $user =  Auth::user();
            $props = $order->property()->get();
            $expected_returns = (($props[0]->net_rental_yield / 100) * $order->price) + $order->price;
            $order->expected_returns = $expected_returns;
            $order->update();
            $data = [
                'property_name' => $props[0]->name,
                'fraction_qty' => $order->fractions_qty,
                'price' => $this->formatMoney($order->price),
                'expected_returns' => $this->formatMoney($expected_returns),
                'end_date' => $order->end_date->toDayDateTimeString(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
            ];

            Mail::to($user->email, $user->firstname)->send(new OrderSuccessMail($data));

            return $this->sendResponse($order, 'Order created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Show a single order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $order = Order::with(['property', 'user'])->find($id);
            if (!$order) {
                return $this->sendError('Order not found', null, 404);
            }
            return $this->sendResponse($order, 'Order fetched successfully', 200);
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
            $order = Order::find($id);
            if (!$order) {
                return $this->sendError('Order not found', null, 404);
            }
            $order->delete();
            return $this->sendResponse(null, 'Order deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Verify payment from flutterwave.
     *
     * @param  Array  $data
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(Request $request)
    {
        try {
            //get the txref, qty, and property price
            $txref = $request->input('txref');
            $qty = $request->input('qty');
            $property_id = $request->input('property_id');
            $price = Property::find($property_id)->min_fraction_price;

            $client = new Client();
            $url = getenv('LIVE_VERIFY_URL');
            $res = $client->request('POST', $url, [
                'form_params' => [
                    'txref' => $txref,
                    'SECKEY' => getenv('FLUTTERWAVE_SECRET_KEY'),
                ],
                'header' => ['Content-Type' => 'application/json']
            ]);
            $data = collect(json_decode(utf8_decode($res->getBody()->getContents()), true));

            //Verify payment
            // @codingStandardsIgnoreLine
            if (
                $data['status'] == 'success' &&
                $data['data']['txref'] == $txref &&
                $data['data']['currency'] == 'NGN' &&
                $data['data']['amount'] >= $price * $qty
            ) {
                return $this->sendResponse(null, 'Payment verified');
            }
            return $this->sendError('error', 'Invalid Payment', 409);
        } catch (\Exception  $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
