<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::with('role', 'company')->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ? $user->role->name : null,  // Pastikan ini string
            'company' => $user->company,
        ],
        'token' => $token,
        'role' => $user->role ? $user->role->name : null,  // Pastikan ini string
    ]);
}

  public function me(Request $request)
{
    $user = $request->user()->load('role', 'company');
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role ? $user->role->name : null,  // Pastikan ini string
        'company' => $user->company,
        'phone' => $user->phone,
        'nisn' => $user->nisn,
    ]);
}
}