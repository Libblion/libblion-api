<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'required' => 'inputan :attribute wajib diisi',
            'min' => 'inputan :attribute minimal :min karakter',
            'email' => 'inputan :attribute harus berformat email',
            'unique' => 'inputan :attribute sudah terdaftar',
            'confirmed' => 'inputan password tidak sama dengan confirmation password',
        ]);

        $user = new User;
        $role = Role::where('name', 'user')->first();

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role_id = $role->id;

        $user->save();
        return response()->json([
            'message' => 'Berhasil mendaftar',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required',
            'password' => 'required',
        ], [
            'required' => 'inputan :attribute wajib diisi',
        ]);
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        return response()->json([
            'message' => 'berhasil login',
            'user' => $user,
            'token' => $token

        ], 200);
    }


    public function currentUser()
    {
        $user = auth()->user();
        $userData = User::with(['role', 'profile', 'borrowings', 'reviews'])->find($user->id);

        return response()->json([
            'message' => 'user ditemukan',
            'user' => $userData
        ], 200);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'logout berhasil'
        ]);
    }
}