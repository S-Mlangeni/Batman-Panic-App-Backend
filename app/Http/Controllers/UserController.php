<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function login(Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                "status" => "error",
                "message" => "The provided credentials are incorrect.",
                "data" => []
            ], 400);
        };
        
        $token = $user->createToken("panic_app_token")->plainTextToken;
        
        return response([
            "status" => "success",
            "message" => "Action completed successfully",
            "data" => [
                "api_access_token" => $token
            ]
        ], 200);
    }

    function logout() {
        $user = request()->user();
        $user->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "Logged out successfully",
            "data" => []
        ]);
    }
}
