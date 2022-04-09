<?php

namespace Avanahuda\Avanatest\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exception;
use Avanahuda\Avanatest\Models\Customer;
use Avanahuda\Avanatest\Models\Payment;
use Avanahuda\Avanatest\Models\Order;
use Avanahuda\Avanatest\Models\OrderDetail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offset = $request->input('offset') ? $request->input('offset') : 0;
        $limit = $request->input('limit') ? $request->input('limit') : 10;
        $query = DB::table("orders")->offset($offset)->limit($limit)->get();
        return response()->json([
            'success' => TRUE,
            'data' => $query,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_date' => 'required|date',
            'discount' => 'required|date',
            'email' => 'required|email',
            'amount_paid' => 'required|number|min:0',
            'details' => 'required|array|min:1'
        ]);

        $email = $request->input('email');
        $cust = Customer::firstWhere('email', $email);
        if (is_null($cust)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'unknown customer',
            ]);
        }
        try {
            //insert order
            $orderDetailCollect = collect($request->input('details'));
            $orderSubTotal = $orderDetailCollect->sum('total');
            $order = Order::create([
                'order_date' => $request->input('order_date'),
                'discount' => $request->input('discount'),
                'sub_total' => $orderSubTotal,
                'customer_id' => $cust->id,
            ]);
            DB::commit();
            if ($order && $order->id) {
                //insert order details
                $oDetailsStored = array();
                foreach($orderDetailCollect as $item) {
                    $newItem = $item;
                    $newItem['order_id'] = $order->id;
                    array_push($oDetailsStored, $newItem);
                }
                $insertOrderDetails = OrderDetail::insert($oDetailsStored);

                //insert payment
                if ($request->input('amount_paid') > 0) {
                    $insertOrderDetails = Payment::create([
                        'order_id' => $order->id,
                        'status' => 1,
                        'amount_paid' => $request->input('amount_paid'),
                        'payment_date' => date('Y-m-d'),
                    ]);
                }
            }
            return response()->json([
                'success' => TRUE,
                'data' => $order,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => FALSE,
                'data' => "Create order failed",
            ]);
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
        $find = Customer::find($id);
        return response()->json([
            'success' => $find ? TRUE : FALSE,
            'data' => $find ? $find : NULL,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $findOrder = Order::find($id);
        if (is_null($findOrder)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'order data not found',
            ]);
        }
        //check customer_email
        $email = $request->input('email');
        $cust = Customer::firstWhere('email', $email);
        if (is_null($cust)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'unknown customer',
            ]);
        }

        try {
            //update order
            $orderDetailCollect = collect($request->input('details'));
            $orderSubTotal = $orderDetailCollect->sum('total');
            $order = $findOrder->update([
                'order_date' => $request->input('order_date'),
                'discount' => $request->input('discount'),
                'sub_total' => $orderSubTotal,
                'customer_id' => $cust->id,
            ]);
            DB::commit();

            if ($findOrder && $findOrder->id) {

                //delete previous order_detail and payment data
                $orderDetailsCount = DB::table('order_details')->where('order_id', $findOrder->id)->count();
                if ($orderDetailsCount > 0 ) {
                    DB::table('order_details')->where('order_id', $findOrder->id)->delete();
                }
                $paymentCheckCount = DB::table('payments')->where('order_id', $findOrder->id)->count();
                if ($paymentCheckCount > 0 ) {
                    DB::table('payments')->where('order_id', $findOrder->id)->delete();
                }

                //update order details
                $oDetailsStored = array();
                foreach($orderDetailCollect as $item) {
                    $newItem = $item;
                    $newItem['order_id'] = $findOrder->id;
                    array_push($oDetailsStored, $newItem);
                }
                $insertOrderDetails = OrderDetail::insert($oDetailsStored);

                //update payment
                if ($request->input('amount_paid') > 0) {
                    $insertOrderDetails = Payment::create([
                        'order_id' => $findOrder->id,
                        'status' => 1,
                        'amount_paid' => $request->input('amount_paid'),
                        'payment_date' => date('Y-m-d'),
                    ]);
                }
            }

            $findOrder = Order::find($id);

            return response()->json([
                'success' => TRUE,
                'data' => $findOrder,
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => FALSE,
                'data' => 'Edit order failed',
            ]);
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
        $orderCheck = Order::where('id', $id)->first();
        if (is_null($orderCheck)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'Order data not found'
            ]);
        }

        try {
            DB::table('order_details')->where('order_id', $id)->delete();
            DB::table('payments')->where('order_id', $id)->delete();
            DB::commit();

            $doDelete = Order::destroy($id);

            return response()->json([
                'success' => TRUE,
                'data' => 'order data deleted'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => FALSE,
                'data' => 'problem when delete order data'
            ]);
        }
    }

}
