<?php

namespace Avanahuda\Avanatest\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exception;
use Avanahuda\Avanatest\Models\Customer;
use Avanahuda\Avanatest\Models\Order;

class CustomerController extends Controller
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
        $query = DB::table("customers")->offset($offset)->limit($limit)->get();
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
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone_number' => 'required'
        ]);
        try {
            $userEmailCheck = Customer::where('email', $request->input('email'))->first();
            if ($userEmailCheck) {
                return response()->json([
                    'success' => FALSE,
                    'data' => 'email already taken',
                ]);
            }
            $show = Customer::create($validatedData);
            return response()->json([
                'success' => TRUE,
                'data' => $show,
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
        $find = Customer::where('email', $id)->first();
        return response()->json([
            'success' => $find ? TRUE : FALSE,
            'data' => $find ? $find : 'customer not found',
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
        $cust = Customer::where('email', $id)->first();
        if (is_null($cust)) {
            return response()->json([
                'success' => FALSE,
                'data' => 'customer not found',
            ]);
        }

        try {
            $cust->update($request->all());

            return response()->json([
                'success' => $cust ? TRUE : FALSE,
                'data' => $cust ? $cust : NULL,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => $cust ? TRUE : FALSE,
                'data' => $cust ? $cust : NULL,
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
        $custOrderCheck = Order::where('customer_id', $id);
        if ($custOrderCheck) {
            return response()->json([
                'success' => FALSE,
                'data' => 'Cannot delete customer, because he/she has order data'
            ]);
        }
        $doDelete = Customer::destroy($id);
        return response()->json([
            'success' => $doDelete ? TRUE : FALSE
        ]);
    }

}
