<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
//for email authentication
use Illuminate\Auth\Events\Registered;
//for hashing password
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);
        DB::beginTransaction();
        try {
            $register = User::create($validated);
            //trigger for email verification
            event(new Registered($register));
            DB::commit();
            return response()->json([
                'message' => 'User created and email confirmation send to your email',
                'data' => $register,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to perform registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8'
        ]);
        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
        //generate token 
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    public function logout(Request $request)
    {
        //delete token
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Successfull'
        ], 201);
    }
    public function assignBranch(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $user->branches()->syncWithoutDetaching([
                $validated['branch_id']
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Branch assigned to user',
                'data' => $user->load('branches'),
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Unable to assign branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
