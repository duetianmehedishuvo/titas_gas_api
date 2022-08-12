<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\TransactionModel;
use App\Models\MeterModel;
use App\Models\RegistrationModel;

class TransactionController extends Controller
{
    function addTranaction(Request $request)
    {
        
        $user_id = $request->input('user_id');
        $meter_id = $request->input('meter_id');
        $date=date('Y-m-d H:i:s');
        $reportNo=time();
        $comment=$request->input('comment');
        $fileData=$request->file('reportImage');

        $reportImage='';

        $meterCount = MeterModel::where(["meterID" => $meter_id])->count();
        $userCount = RegistrationModel::where('user_id', $user_id)->count();
        if ($meterCount == 1 && $userCount == 1) {
            
            if($fileData!=null){
                $imageUrl=time().$fileData->getClientOriginalName();
                $fileData->move('images', $imageUrl);
                $reportImage=$imageUrl;
            }else{
                $reportImage='no-image-found.jpg';
            }

            $result = TransactionModel::insert([
                
                'userID' => $user_id,
                'meterID' => $meter_id,
                'Date' => $date,
                'ReportNo' => $reportNo,
                'Comment' => $comment,
                'ReportImage' => $reportImage

            ]);

            if ($result == true) {
                return response()->json(['message' => 'Transaction Added Successfully', 'statusCode' => 200])->setStatusCode(200);
        
            } else {
                return response()->json(['message' => 'Transaction Added Failed', 'statusCode' => 404])->setStatusCode(404);
        
            }
            
        } else if($meterCount == 0){
            return response()->json(['message' => 'Meter Not Found', 'statusCode' => 404])->setStatusCode(404);
        }else {
            return response()->json(['message' => 'User Not Found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

    function getAllTransctionList(Request $request)
    {
        return TransactionModel::all();
    }

    function getTranactionByID(Request $request)
    {
        
        $userID = $request->input('user_id');
        $meterID= $request->input('meter_id');
        $isSearchUser=$request->input('isSearchUser');
        $meterCount = MeterModel::where(["meterID" => $meterID])->count();
        $userCount = RegistrationModel::where('user_id', $userID)->count();

        if($isSearchUser==1){
            if($userCount!=0){
                return TransactionModel::where(['userID' => $userID])->get();
            }else{
                return response()->json(['message' => 'No User Found', 'statusCode' => 404])->setStatusCode(404);
            }
            
        }else{
            if($meterCount!=0){
                return TransactionModel::where(['meterID' => $meterID])->get();
            }else{
                return response()->json(['message' => 'No Meter Found', 'statusCode' => 404])->setStatusCode(404);
            }
        }

    }


}
