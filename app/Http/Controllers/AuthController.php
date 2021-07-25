<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Validator;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except('login');
    }

    public function login(Request $request){
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $messages = [
            'required' => ':attribute harus disi'
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if($validation->fails()) {
            return response()->json([
                'message' => '',
                'status' =>  409,
                'errors' => $validation->errors(),
            ], 409);
        }

        $attempts = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if(Auth::attempt($attempts)){
            $user = Auth::user();
            $user['token'] = $user->createToken(env('PASSPORT_KEY_APP'))->accessToken;

            return response()->json([
                'message' => 'Login berhasil',
                'status' => 200,
                'data' => $user
            ], 200);
        }else{
            return response()->json([
                'message'=>'Login gagal',
                'status' => 401,
                'data' => 'Username or Password salah'
            ], 401); 
        }
    }

    public function saveToken(Request $request){
        auth()->user()->update(['device_token' => $request->device_token]);
        return response()->json([
            'message' => 'Berhasil',
            'status' => 200
        ], 200);
    }
}
