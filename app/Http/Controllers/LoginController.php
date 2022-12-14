<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use App\Models\RegistrationModel;

class LoginController extends Controller
{
    function onLogin(Request $request)
    {
        
        $job_code = $request->input('job_code');
        $password = $request->input('password');
        $userCount = RegistrationModel::where(["job_code" => $job_code, "password" => $password,])->count();
        if ($userCount == 1) {

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
                return response()->json(['message' => ' Login Success','token' => $jwt,  'user' => $user]);
                
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);

        }
    }

    function tokenTest()
    {
        return 'token is Okay';
    }

    function changePassword(Request $request)
    {
        $job_code = $request->input('job_code');
        $password = $request->input('password');
        $new_password = $request->input('new_password');
        $userCount = RegistrationModel::where(["job_code" => $job_code, "password" => $password,])->count();
        if ($userCount == 1) {
            $result = RegistrationModel::where(["job_code" => $job_code, "password" => $password,])->update([
                'password' => $new_password
            ]);
            if ($result == true) {
                return response()->json(['message' => 'password Update Success', 'statusCode' => 200])->setStatusCode(200);
            } else {
                return response()->json(['message' => 'Fail ! Try Again', 'statusCode' => 404])->setStatusCode(404);
            }
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

    function logout(Request $request)
    {
        $job_code = $request->input('job_code');
        $password = $request->input('password');
        $userCount = RegistrationModel::where(["job_code" => $job_code, "password" => $password,])->count();
        if ($userCount == 1) {
            return response()->json(['message' => 'Logout Success', 'statusCode' => 200])->setStatusCode(200);
        } else {
            return response()->json(['message' => 'User not found', 'statusCode' => 404])->setStatusCode(404);
        }
    }

}
