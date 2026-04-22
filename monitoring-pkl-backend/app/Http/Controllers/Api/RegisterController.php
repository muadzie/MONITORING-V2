<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    // Register untuk Siswa
    public function registerSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'nisn' => 'required|string|unique:users,nisn',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nisn' => $request->nisn,
            'phone' => $request->phone,
            'address' => $request->address,
            'role_id' => 2, // role siswa
            'registration_status' => 'pending',
            'is_active' => false
        ]);

        // Kirim notifikasi ke admin
        $this->sendNotificationToAdmin($user, 'Siswa');

        return response()->json([
            'message' => 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari admin.',
            'user' => $user
        ], 201);
    }

    // Register untuk Guru
    public function registerGuru(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'nip' => 'required|string|unique:users,nomor_induk',
            'phone' => 'nullable|string|max:15',
            'mata_pelajaran' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nomor_induk' => $request->nip,
            'phone' => $request->phone,
            'role_id' => 3, // role guru
            'registration_status' => 'pending',
            'is_active' => false
        ]);

        $this->sendNotificationToAdmin($user, 'Guru');

        return response()->json([
            'message' => 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari admin.',
            'user' => $user
        ], 201);
    }

    // Register untuk Perusahaan
    public function registerPerusahaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'nama_perusahaan' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            'bidang_usaha' => 'required|string',
            'kontak_person' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 4, // role perusahaan
            'registration_status' => 'pending',
            'is_active' => false
        ]);

        // Simpan data perusahaan ke tabel perusahaan
        $perusahaan = \App\Models\Company::create([
            'user_id' => $user->id,
            'name' => $request->nama_perusahaan,
            'address' => $request->alamat,
            'phone' => $request->telepon,
            'bidang_usaha' => $request->bidang_usaha,
            'kontak_person' => $request->kontak_person,
            'email' => $request->email,
        ]);

        $this->sendNotificationToAdmin($user, 'Perusahaan');

        return response()->json([
            'message' => 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari admin.',
            'user' => $user
        ], 201);
    }

    // Kirim notifikasi ke admin
    private function sendNotificationToAdmin($user, $role)
    {
        $admins = User::where('role_id', 1)->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Pendaftaran Baru',
                'message' => "Pendaftaran baru dari {$user->name} sebagai {$role}. Silakan verifikasi.",
                'type' => 'info',
                'url' => '/admin/registrations',
                'is_read' => false
            ]);
        }
    }
}