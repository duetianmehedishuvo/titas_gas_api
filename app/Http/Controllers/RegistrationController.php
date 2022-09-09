<?php

namespace App\Http\Controllers;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\RegistrationModel;

class RegistrationController extends Controller
{
    function onRegister(Request $request)
    {
        $name = $request->input('name');
        $section = $request->input('section');
        $designation = $request->input('designation');
        $phone = $request->input('phone');
        $job_code = $request->input('job_code');
        $password = $request->input('password');
        $current_date = date('Y-m-d H:i:s');
        $userCount = RegistrationModel::where('job_code', $job_code)->count();
        if ($userCount == 0) {
            $result = RegistrationModel::insert([
                'name' => $name,
                'section' => $section,
                'designation' => $designation,
                'phone' => $phone,
                'password' => $password,
                'job_code' => $job_code,
                'isUpdate' => 0,
                'isDelete' => 0,
                'isAdd' => 0,
                'isView' => 1,
                'isAdmin' => 0,
                'created_at' => $current_date,
                'updated_at' => $current_date

            ]);

            if ($result == true) {
                $user = RegistrationModel::where(["job_code" => $job_code, "password" => $password,])->first();
            $key = env('TOKEN_KEY');
                $payload = array(
                    "site" => "http://demo.com",
                    "email" => $user->email,
                    "iat" => time(),
                    "exp" => time() + 3600,
                    "id" => $user->job_code,
                );
                $jwt = JWT::encode($payload, $key, 'HS256');
                return response()->json(['message' => 'Registration Succesfull','token' => $jwt,  'user' => $user])->setStatusCode(200);

            } else {
                return response()->json(['message' => 'Registration Fail Try Again', 'statusCode' => 404])->setStatusCode(404);
                
            }

        } else {
            return response()->json(['message' => 'User Already Exists', 'statusCode' => 404])->setStatusCode(404);
        }

    }

    function getAllUser(Request $request)
    {
        return RegistrationModel::all();
    }

    function getUserByID(Request $request)
    {
        $job_code = $request->input('job_code');
        $userCount = RegistrationModel::where('job_code', $job_code)->count();
        if ($userCount >= 1) {
            return RegistrationModel::where('job_code', $job_code)->first();
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

    function updateUser(Request $request)
    {
        $job_code = $request->input('job_code');
        $name = $request->input('name');
        $section = $request->input('section');
        $designation = $request->input('designation');
        $phone = $request->input('phone');
        $current_date = date('Y-m-d H:i:s');
        $isUpdate=$request->input('isUpdate');
        $isDelete=$request->input('isDelete');
        $isAdd=$request->input('isAdd');
        $isView=$request->input('isView');
        $isAdmin=$request->input('isAdmin');
        $userCount = RegistrationModel::where('job_code', $job_code)->count();
        if ($userCount >= 1) {
            $result = RegistrationModel::where('job_code', $job_code)->update([
                'name' => $name,
                'section' => $section,
                'designation' => $designation,
                'phone' => $phone,
                'isUpdate' => $isUpdate,
                'isDelete' => $isDelete,
                'isAdd' => $isAdd,
                'isView' => $isView,
                'isAdmin' => $isAdmin,
                'updated_at' => $current_date
            ]);
            if ($result == true) {
                return response()->json(['message' => 'User Update successfull.', 'statusCode' => 200])->setStatusCode(200);
            } else {
                return response()->json(['message' => 'User not Updated', 'statusCode' => 404])->setStatusCode(404);
            }
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

    function getUserProfile(Request $request)
    {
        $access_token = str_replace('Bearer ', '', $request->header('Authorization'));
        $key = env('TOKEN_KEY');
        $decoded = JWT::decode($access_token, new Key($key, 'HS256'));
        $decoded_array = (array)$decoded;
        $job_code = $decoded_array['id'];
        $userCount = RegistrationModel::where('job_code', $job_code)->count();
        if ($userCount >= 1) {
            return RegistrationModel::where('job_code', $job_code)->first();
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

}
