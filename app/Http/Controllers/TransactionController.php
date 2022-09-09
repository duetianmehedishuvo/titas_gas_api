<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\TransactionModel;
use App\Models\MeterModel;
use App\Models\CustomerModel;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    function addTranaction(Request $request)
    {
        
        $customer_id = $request->input('customer_id');
        $meter_id = $request->input('meter_id');
        $date=      $request->input('date');
        $reportNo=$request->input('reportNo');
        $comment=$request->input('comment');
        $fileData=$request->file('reportImage');

        $reportImage='';

        $meterCount = MeterModel::where(["meterID" => $meter_id])->count();
        $userCount = CustomerModel::where('customer_id', $customer_id)->count();
        
        $query = 'SELECT MAX(id) as id FROM transactiontable group by customer_id;';
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
                $imageUrl=$reportNo.'.'.$fileData->extension();
                $fileData->move('images', $imageUrl);
                $reportImage=$imageUrl;
            }else{
                $reportImage='no-image-found.jpg';
            }

            $result = TransactionModel::insert([
                
                'customer_id' => $customer_id,
                'meterID' => $meter_id,
                'createDate' => date('Y-m-d', strtotime($date)),
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
            return response()->json(['message' => 'Customer Not Found', 'statusCode' => 404])->setStatusCode(404);
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
        
        $customer_id = $request->input('customer_id');
        $meterID= $request->input('meter_id');
        $isSearchUser=$request->input('isSearchUser');
        $meterCount = MeterModel::where(["meterID" => $meterID])->count();
        $userCount = CustomerModel::where('customer_id', $customer_id)->count();

        if($isSearchUser==1){
            if($userCount!=0){
                return TransactionModel::where(['customer_id' => $customer_id])->get();
            }else{
                return response()->json(['message' => 'No Customer Found', 'statusCode' => 404])->setStatusCode(404);
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

    function updateTransaction(Request $request)
    {
        
        $id = $request->input('id');
        $date=      $request->input('date');
        $reportNo=$request->input('reportNo');
        $comment=$request->input('comment');
        $fileData=$request->file('reportImage');
        $reportImage='';
        
        if($fileData!=null){
           
            $imageUrl=$reportNo.'.'.$fileData->extension();
            $fileData->move('images', $imageUrl);
            $reportImage=$imageUrl;
            
            $result = TransactionModel::where(['id' => $id])->update([
                'createDate' => date('Y-m-d', strtotime($date)),
                'Comment' => $comment,
                'reportNo' => $reportNo,
                'ReportImage' => $reportImage
            ]);
        }else{
            
            $result = TransactionModel::where(['id' => $id])->update([
                'createDate' => date('Y-m-d', strtotime($date)),
                'reportNo' => $reportNo,
                'Comment' => $comment,
            ]);
        }

        if ($result == true) {
            return response()->json(['message' => 'Transaction Updated Successfully', 'statusCode' => 200])->setStatusCode(200);
        } else {
            return response()->json(['message' => 'Transaction Updated Failed, Please Change Somethings', 'statusCode' => 404])->setStatusCode(404);
    
        }
}

function deleteTransaction(Request $request)
{
    
    $id = $request->input('id');

    $result = TransactionModel::where(['id' => $id])->delete();
    if ($result == true) {
        return response()->json(['message' => 'Transaction Deleted Successfully', 'statusCode' => 200])->setStatusCode(200);

    } else {
        return response()->json(['message' => 'Transaction Deleted Failed', 'statusCode' => 404])->setStatusCode(404);

    }
}

}
