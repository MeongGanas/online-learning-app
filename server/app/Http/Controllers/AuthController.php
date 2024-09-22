<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            $user = Administrator::where('username', $request->username)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "status" => "authentication_failed",
                "message" => "The username or password you entered is incorrect"
            ], 400);
        }

        $user["token"] = $user->createToken($user->username)->plainTextToken;

        return response()->json(["status" => "success", "message" => "Login successful", "data" => $user], 201);
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string',
                'username' => 'required|string|min:3|max:255|regex:/^[a-zA-Z0-9._]+$/|unique:users|unique:administrators',
                'password' => 'required|string|min:6|max:255'
            ]);

            $user = User::create([
                'full_name' => $request->full_name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            $user["token"] = $user->createToken($user->username)->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "User registration successful",
                "data" => $user
            ], 201);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => $error->getMessage(),
                "errors" => $error->errors()
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                "status" => "success",
                "message" => "Logout successful"
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 400);
        }
    }
}
