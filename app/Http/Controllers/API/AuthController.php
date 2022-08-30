<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);


            $user = User::where('email', $request->email)
                ->first();

            if (!$user) {
                throw new Exception("Authentication Failed", 401);
            }

            $credentials['email'] = $request->email;
            $credentials['password'] = $request->password;

            if (!Auth::attempt($credentials)) {
                throw new Exception("Authentication Failed", 401);
            }

            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception("Email or password doesn't match", 400);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login Successfully',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            if (!$user->save()) {
                throw new Exception('Failed transaction DB!', 500);
            }

            DB::commit();
            return response()->json([
                'data' => $user,
                'message' => 'Registration Successfuly!'
            ], 201);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $profile = Auth::user();

            return response()->json([
                'message' => 'Fetching data profile successfully',
                'data' => $profile
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Token revoked',
            'data' => $user
        ], 200);
    }
}
