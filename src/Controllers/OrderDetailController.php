<?php

namespace Avanahuda\Avanatest\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exception;
use Avanahuda\Avanatest\Models\Order;
use Avanahuda\Avanatest\Models\OrderDetail;

class OrderDetailController extends Controller
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
        $query = DB::table("order_details")->offset($offset)->limit($limit)->get();
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
        $orderId = $request->input('order_id');
        $findOrder = Order::find($orderId);
        if (is_null($findOrder)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'order data not found',
            ]);
        }

        try {
            $newTotal =  ((double) $request->input('qty') * (double) $request->input('price')) - (double) $request->input('discount');
            $insertOrderDetail = OrderDetail::create(array_merge($request->all(), ['total' => $newTotal]));

            $currentSubTotal = $findOrder->sub_total;
            $findOrder->update([
                'sub_total' => $currentSubTotal + $newTotal
            ]);
            DB::commit();
            return response()->json([
                'success' => TRUE,
                'data' => $insertOrderDetail
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => FALSE,
                'data' => 'order data not found',
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
        $find = OrderDetail::where('id', $id)->first();
        return response()->json([
            'success' => $find ? TRUE : FALSE,
            'data' => $find ? $find : 'data not found',
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
        $find = OrderDetail::where('id', $id)->first();
        if (is_null($find)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'data not found',
            ]);
        }

        $currentOrderDetailTotal = (double) $find->total;
        $newTotal =  ((double) $request->input('qty') * (double) $request->input('price')) - (double) $request->input('discount');
        try {
            $checkOrder = Order::find($find->order_id);
            $currentSubTotal = $checkOrder->sub_total;
            $checkOrder->update([
                'sub_total' => ($currentSubTotal - $currentOrderDetailTotal) + $newTotal
            ]);
            $find->update(array_merge($request->all(), ['total' => $newTotal]));
            DB::commit();
            return response()->json([
                'success' => TRUE,
                'data' => $find
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => FALSE,
                'data' => 'problem found',
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
        $orderDetailCheck = OrderDetail::where('id', $id)->first();
        if (is_null($orderDetailCheck)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'Order Detail not found'
            ]);
        }

        try {
            $orderCheck = Order::where('id', $orderDetailCheck->order_id)->first();

            DB::table('orders')->where('id', $orderCheck->id)->update([
                'sub_total' => (double) $orderCheck->sub_total - (double) $orderDetailCheck->total
            ]);
            DB::commit();

            $doDelete = OrderDetail::destroy($id);

            return response()->json([
                'success' => TRUE,
                'data' => 'order detail deleted'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => FALSE,
                'data' => 'problem when delete order detail'
            ]);
        }
    }

}
