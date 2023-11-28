<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request) {
        try {
            //code...

            $rules = $request->only(['name', 'email', 'password', 'relation']);
            
            $validator = Validator::make($rules, [
                'name' => 'required|string|min:5',
                'email' => 'required|string|email|unique:users'
                'password' => 'required|string|min:5',
                
            ]);
            
            return response()->json($rules, 200);

        } catch (Exception $err) {
            
            return (object) [
                'code' => 500,
                'msg' => 'Internal server error',
            ];
        }
    }
}
