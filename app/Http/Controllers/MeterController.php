<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\MeterModel;
use App\Models\TransactionModel;
use Illuminate\Support\Facades\DB;

class MeterController extends Controller
{
    
    
    function onSelect(Request $request)
    {
        return MeterModel::all();
    }

    function onInsert(Request $request)
    {

        $year = $request->input('year');
        $meterID = $request->input('meter-id');
        $result = MeterModel::insert([
            'year' => $year,
            'meterID' => $meterID
        ]);
        if ($result == true) {
            return "Save Success ";
        } else {
            return "Fail ! Try Again";
        }
    }


}
