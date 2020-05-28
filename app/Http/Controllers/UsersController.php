<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\User;

class UsersController extends Controller
{
    /**
     * Create a new UsersController instance.
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
    public function allUserOrder()
    {
        try {
            $user = User::find(Auth::id());
            $user_orders = Cache::remember('user:' . $user->id, 1800, function () use ($user) {
                return $user->orders()->with(['property.category'])->get();
            });
            return $this->sendResponse($user_orders, 'Orders fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allUserInvoice()
    {
        try {
            $user = User::find(Auth::id());
            $user_invoice = Cache::remember('user:' . $user->id, 1800, function () use ($user) {
                return $user->invoices()->with(['property.category'])->get();
            });
            return $this->sendResponse($user_invoice, 'Invoices fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function oneUserOrder($id)
    {
        try {
            $user = User::find(Auth::id());
            $user_order = $user->orders()->with(['property'])->find($id);
            if (!$user_order) {
                return $this->sendError('Order not found', null, 404);
            }
            return $this->sendResponse($user_order, 'Order fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function oneUserInvoice($id)
    {
        try {
            $user = User::find(Auth::id());
            $user_invoice = $user->invoices()->with(['property'])->find($id);
            if (!$user_invoice) {
                return $this->sendError('Invoice not found', null, 404);
            }
            return $this->sendResponse($user_invoice, 'Invoice fetched successfully');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage(), 409);
        }
    }
}
