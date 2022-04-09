<?php

namespace Avanahuda\Avanatest\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exception;
use Avanahuda\Avanatest\Models\Payment;

class PaymentController extends Controller
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
        $query = DB::table("payments")->offset($offset)->limit($limit)->get();
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
            'order_id' => 'required',
            'amount_paid' => 'required',
            'payment_date' => 'required|date'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => FALSE,
                'data' => 'data / param is not valid',
            ]);
        }
        try {
            $pay = Payment::create(array_merge($request->all(), ['status' => 1]));
            return response()->json([
                'success' => TRUE,
                'data' => $pay,
            ]);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json([ 'success' => FALSE, 'data' => $e ]);
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
        $find = Payment::where('id', $id)->first();
        return response()->json([
            'success' => $find ? TRUE : FALSE,
            'data' => $find ? $find : 'payment not found',
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
        $payment = Payment::where('id', $id)->first();
        if (is_null($payment)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'payment not found',
            ]);
        }

        try {
            $payment->update($request->all());

            return response()->json([
                'success' => $payment ? TRUE : FALSE,
                'data' => $payment ? $payment : NULL,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => $payment ? TRUE : FALSE,
                'data' => $payment ? $payment : NULL,
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
        $paymentCheck = Payment::find($id);
        if (is_null($paymentCheck)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'Payment not found'
            ]);
        }
        $doDelete = Payment::destroy($id);
        return response()->json([
            'success' => $doDelete ? TRUE : FALSE
        ]);
    }

}
