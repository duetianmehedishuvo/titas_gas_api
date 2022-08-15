<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\TransactionModel;
use App\Models\MeterModel;
use App\Models\RegistrationModel;
use Illuminate\Support\Facades\DB;

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
        
        $query = 'SELECT MAX(id) as id FROM transactiontable group by userID;';
        $result=DB::select($query);
        
        $meterStatus=0;
        $arrLength = count($result);

        for($i = 0; $i < $arrLength; $i++) {
            $query111='SELECT * FROM transactiontable WHERE id ='.$result[$i]->id.';';
            $res=DB::select($query111);

            if($res[0]->meterID== $meter_id) {
                $meterStatus = 1;
                break;
            }   
        }

        if($meterStatus == 0) {
            
        if ($meterCount >= 1 && $userCount >= 1) {
            
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
                'createDate' => $date,
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
        
        }else{
            return response()->json(['message' => 'Meter Already User', 'statusCode' => 404])->setStatusCode(404);
        }

    }

    function getAllTransctionList(Request $request)
    {
        
        return TransactionModel::orderby('id','desc')->get();
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

    function getAllTranactionOrder(Request $request)
    {
        $listAllMeterID=TransactionModel::orderBy('createDate','DESC')->orderBy('userID','DESC')->orderBy('ReportNo','DESC')->distinct()->pluck('meterID');
        print($listAllMeterID."\n");

        $normallistAllMeterID=TransactionModel::orderBy('createDate','DESC')->orderBy('userID','DESC')->orderBy('ReportNo','DESC')->get();
        // print($normallistAllMeterID);
        $arrLength = count($listAllMeterID);
        for($i = 0; $i < $arrLength; $i++) {

            //echo $listAllMeterID[$i].'<br>';
            $normallis=TransactionModel::where('meterID',$listAllMeterID[$i])->orderBy('createDate','DESC')->orderBy('userID','DESC')->orderBy('ReportNo','DESC')->get();
            echo $normallis.'<br>';
        
        
        }

        return $arrLength;
    }

    function getUniqueId(Request $request)
    {
        
        $query = 'SELECT MAX(id) as id FROM transactiontable group by userID;';
        $result=DB::select($query);
        
        $meterStatus=0;
        $arrLength = count($result);
        for($i = 0; $i < $arrLength; $i++) {
            print_r($result[$i]->id);
        }
        
        return $result;
    }

}
