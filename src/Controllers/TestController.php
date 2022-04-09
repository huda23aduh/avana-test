<?php

namespace Avanahuda\Avanatest\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exception;

class TestController extends Controller
{
    public function index($timezone)
    {
        echo "abbb";
    }

    public function viewData(Request $request)
    {
        echo "q";
    }

    public function createOrderDummy(Request $request)
    {
        $input = $request->all();
        dd($input);
    }

    public function createCustomerDummy(Request $request)
    {
        // $query = DB::connection('pgsql')->table('customers');
        DB::beginTransaction();
        try {
            $query = DB::select("select tt from customers");
            $query->get();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA',
        ]);
    }
}
