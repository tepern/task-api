<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    { 
        $data = $request->all();
        
        $validator = Validator::make($data, [
            'name'=> ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8']
        ]); 
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        } 
        
        $user = User::create([
            'name' => $data['name'],
            'password' => bcrypt($data['password']),
            'email' => $data['email']
        ]);  
        
        $token = $user->createToken('user_token')->plainTextToken;  
        
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function login(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        } 

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
        }
     
        return response()->json(['token' => $user->createToken('user_token')->plainTextToken]);
    }
}
