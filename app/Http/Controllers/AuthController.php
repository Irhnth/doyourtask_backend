<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        // Cari level pertama (Level 1) untuk user baru
        $firstLevel = Level::where('level_number', 1)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'current_xp' => 0,
            'level_id' => $firstLevel ? $firstLevel->id : null
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial tidak valid'], 401);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        // Muat data relasi (Level dan Badges) untuk ditampilkan di aplikasi
        $user->load('level', 'badges');

        return response()->json(['user' => $user, 'token' => $token], 200);
    }
}