<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistrationModel;

class RegistrationController extends Controller
{
    function onRegister(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $userID = $request->input('userID');
        $password = $request->input('password');
        $current_date = date('Y-m-d H:i:s');
        $userCount = RegistrationModel::where('user_id', $userID)->count();
        if ($userCount == 0) {
            $result = RegistrationModel::insert([
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'password' => $password,
                'user_id' => $userID,
                'isUpdate' => 0,
                'isDelete' => 0,
                'isAdd' => 0,
                'isView' => 1,
                'created_at' => $current_date,
                'updated_at' => $current_date

            ]);

            if ($result == true) {
                return 'Registration Succesfull';
            } else {
                return 'Registration Fail Try Again';
            }

        } else {
            return 'User Already Exists';
        }

    }
}
