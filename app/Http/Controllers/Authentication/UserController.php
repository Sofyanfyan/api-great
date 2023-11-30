<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailQueueJob;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Event\Code\Test;

class UserController extends Controller
{
    public function register(Request $request) {

        DB::beginTransaction();

        try {
            //code...

            $rules = $request->only(['name', 'email', 'password', 'relation']);
            
            $validator = Validator::make($rules, [
                'name' => 'required|string|min:5',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
                'relation' => 'required|string|in:Father,Mother',
            ]);

            if($validator->fails()){
                DB::rollBack();
                return response()->json([
                "code" => 400,
                "msg" => $validator->errors(),
            ], 400);
        }
        
        $regexPassword = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/";
        
        if(!preg_match($regexPassword, $request->password)){
            DB::rollBack();
            return response()->json([
                "code" => 400,
                "msg" => ['password' => [
                    "Password must contain at least one uppercase letter, one lowercase letter, and one number."
                    ]],
                ], 400);
            }
            
            $user = User::create($rules);
            $otp = VerificationCode::create([
                'user_id' => $user->id,
                'otp' => rand(100000, 999999),
                'expire_at' => Carbon::now()->addMinutes(7)->setTimezone('Asia/Jakarta'),
            ]);

            if(!$user || !$otp) {
                DB::rollBack();
                return response()->json([
                    'code' => 409,
                    'msg' => 'Failed create user or otp',
                ], 409);
            }
            
            
            
            DB::commit();

            $credentials = $request->only('email', 'password');
            $token = auth()->guard('api')->attempt($credentials);

            dispatch(new SendEmailQueueJob($credentials['email'], $user, $otp));

            if(!$token = auth()->guard('api')->attempt($credentials)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Failed get access token',
                ], 401);
            }

            return response()->json([
                'code' => 201,
                'user' => auth()->guard('api')->user(),
                'otp' => $otp,
                'access_token' => $token,
            ], 201);

        } catch (Exception $err) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => 'Internal server error',
            ], 500);
        }
    }


    public function login(Request $request) {
        try {
            //code...
            $rules = $request->only(['email', 'password']);
        
            $validator = Validator::make($rules, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validator->fails()) {
                return response()->json([
                    "code" => 400,
                    "msg" => $validator->errors(),
                ], 400);
            }

            if(!$user = User::where('email', $request->email)->first()){
                return response()->json([
                    "code" => 401,
                    "msg" => "Invalid email/password",
                ], 401);
            }

            if(!$token = auth()->guard('api')->attempt($rules)){
                return response()->json([
                    "code" => 401,
                    "msg" => "Invalid email/password",
                ], 401);
            }

            if(!$user->email_verified_at) {

                VerificationCode::where('user_id', $user->id)->update([
                    'otp' => rand(100000, 999999),
                    'expire_at' => Carbon::now()->addMinutes(7)->setTimezone('Asia/Jakarta'),
                ]);

                $otp = VerificationCode::where('user_id', $user->id)->first();

                return response()->json([
                    'code' => 200,
                    'user' => auth()->guard('api')->user(),
                    'otp' => $otp,
                    'token' => $token,
                ],200);
            }

            return response()->json([
                'code' => 200,
                'user' => auth()->guard('api')->user(),
                'access_token' => $token,
            ],200);
            


        } catch (Exception $err) {
            return response()->json([
                'code' => 500,
                'msg' => 'Internal server error',
            ], 500);
        }
    }
}
