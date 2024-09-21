<?php

namespace App\Http\Controllers;

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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                "status" => "authentication_failed",
                "message" => "The username or password you entered is incorrect"
            ];
        }

        $user["token"] = $user->createToken($user->username)->plainTextToken;
        $user["role"] = "user";

        return ["status" => 200, "data" => $user];
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string',
                'username' => 'required|string|min:3|max:255|regex:/^[a-zA-Z0-9._]+$/|unique:users',
                'password' => 'required|string|min:6|max:255'
            ]);

            $user = User::create([
                'full_name' => $request->full_name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            $user["token"] = $user->createToken($user->username)->plainTextToken;
            $user["role"] = "user";

            return [
                "status" => "success",
                "message" => "User registration successful",
                "data" => $user
            ];
        } catch (ValidationException $error) {
            return [
                "status" => "error",
                "message" => $error->getMessage(),
                "errors" => $error->errors()
            ];
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return [
                "status" => "success",
                "message" => "Logout successful"
            ];
        } catch (\Exception $e) {
            return [
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ];
        }
    }
}
