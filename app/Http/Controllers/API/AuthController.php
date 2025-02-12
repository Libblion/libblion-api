<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\GenerateMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\RegisterMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
        Mail::to($user->email)->send(new RegisterMail($user));
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
        ], 200);
    }

    public function generateOtpCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'required' => 'inputan :attribute wajib diisi',
            'email' => 'inputan :attribute harus berformat email'
        ]);

        $user = User::where('email', $request->input('email'))->first();
        $user->generateOtp();

        Mail::to($user->email)->send(new GenerateMail($user));

        return response()->json([
            'message' => 'OTP berhasil di generate, silahkan cek email anda'
        ]);
    }

    public function verifyAccount(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:6',
        ], [
            'required' => 'inputan :attribute wajib diisi',
            'min' => 'inputan maksimal :min karakter '
        ]);

        $user = auth()->user();
        $otp_code = Otp::where('otp', $request->input('otp'))->where('user_id', $user->id)->first();

        if (!$otp_code) {
            return response()->json([
                'message' => 'OTP tidak ditemukan',
            ], 400);
        };

        $now = Carbon::now();
        if ($now > $otp_code->valid_until) {
            return response()->json([
                'message' => 'OTP anda sudah kadaluarsa, silahkan generate ulang OTP anda'
            ], 400);
        }

        $user = User::find($otp_code->user_id);
        $user->email_verified_at = $now;
        $user->save();
        $otp_code->delete();

        return response()->json([
            'message' => 'Verifikasi anda berhasil'
        ], 200);
    }
}
