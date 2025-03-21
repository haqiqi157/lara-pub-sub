<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function fetchAll()
    {
        $users = User::with('notifications')->get();

        return ResponseAPI::success($users, 'success fetch all data users');
    }

    public function findById($id)
    {
        try {
            $user = User::with('notifications')->where('id', $id)->first();

            if (!$user)
            {
                throw new \Exception("there is no user id");
            }

            return ResponseAPI::success($user, 'success find id');
        } catch (\Exception $e)
        {
            return ResponseAPI::error($e->getMessage(), '404');
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'is_admin' => 0
        ]);

        $token = JWTAuth::fromUser($user);

        return ResponseAPI::success([
            'user' => $user,
            'access_token' => $token
        ], 'success register');
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Get the authenticated user.
            $user = auth()->user();

            // (optional) Attach the role to the token.
            $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

            return ResponseApi::success([
                'access_token' => $token
            ], 'success login');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return ResponseApi::success('', 'success logout');
        } catch (\Exception $e) {
            return ResponseApi::error($e->getMessage(), 500);
        }
    }
}
