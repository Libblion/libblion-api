<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileControler extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'address' => 'required',
            'age' => 'required|integer',
            'phone_number' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'required' => 'Inputan :attribute harus diisi',
            'integer' => 'Inputan :attribute harus bernilai angka',
        ]);

        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'address' => $request->input('address'),
                'phone_number' => $request->input('phone_number'),
                'age' => $request->input('age'),
            ]
        );

        if (!$profile) {
            return response()->json([
                'message' => 'Data profile tidak di temukan',
            ], 400);
        }

        if ($request->hasFile('image')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
            $profile->image = $uploadedFileUrl;
        }

        $profile->save();

        return response()->json([
            'message' => 'Berhasil update profile',
        ], 200);
    }
}
