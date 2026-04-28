<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    // Daftar pendaftaran pending
    public function index()
    {
        $registrations = User::with('role')
            ->where('registration_status', 'pending')
            ->where('is_active', false)
            ->get();
        
        return response()->json($registrations);
    }

    // Daftar pendaftaran yang sudah diproses
    public function history()
    {
        $registrations = User::with('role')
            ->whereIn('registration_status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return response()->json($registrations);
    }

    // Approve pendaftaran
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'registration_status' => 'approved',
            'is_active' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // Kirim notifikasi ke user
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Pendaftaran Disetujui',
            'message' => 'Pendaftaran Anda telah disetujui. Silakan login ke sistem.',
            'type' => 'success',
            'url' => '/login',
            'is_read' => false
        ]);

        return response()->json(['message' => 'Pendaftaran disetujui']);
    }

    // Reject pendaftaran
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'registration_status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // Kirim notifikasi ke user
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Pendaftaran Ditolak',
            'message' => "Pendaftaran Anda ditolak. Alasan: {$request->reason}",
            'type' => 'error',
            'url' => '/register',
            'is_read' => false
        ]);

        return response()->json(['message' => 'Pendaftaran ditolak']);
    }
}