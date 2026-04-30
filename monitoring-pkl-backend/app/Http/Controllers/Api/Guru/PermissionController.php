<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PermissionController extends Controller
{
    public function pending()
    {
        $user = auth()->user();
        
        // Ambil semua siswa yang dibimbing oleh guru ini
        $studentIds = User::where('teacher_id', $user->id)->pluck('id');
        
        // Ambil permission yang statusnya pending dari siswa-siswa tersebut
        $permissions = Permission::whereIn('user_id', $studentIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'user_id' => $permission->user_id,
                    'user_name' => $permission->user->name,
                    'user_nisn' => $permission->user->nisn,
                    'date' => $permission->date,
                    'type' => $permission->type,
                    'reason' => $permission->reason,
                    'attachment' => $permission->attachment,
                    'attachment_url' => $permission->attachment ? asset('storage/' . $permission->attachment) : null,
                    'status' => $permission->status,
                    'created_at' => $permission->created_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }
    
    public function history()
    {
        $user = auth()->user();
        
        $studentIds = User::where('teacher_id', $user->id)->pluck('id');
        
        $permissions = Permission::whereIn('user_id', $studentIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'user_id' => $permission->user_id,
                    'user_name' => $permission->user->name,
                    'user_nisn' => $permission->user->nisn,
                    'date' => $permission->date,
                    'type' => $permission->type,
                    'reason' => $permission->reason,
                    'status' => $permission->status,
                    'feedback' => $permission->feedback,
                    'created_at' => $permission->created_at,
                    'updated_at' => $permission->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }
    
    public function approve(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->status = 'approved';
            $permission->feedback = $request->feedback;
            $permission->save();
            
            // ============================================================
            // OTOMATIS BUAT ATAU UPDATE ATTENDANCE DENGAN STATUS SAKIT/IZIN
            // ============================================================
            $this->createOrUpdateAttendance($permission);
            
            return response()->json([
                'success' => true,
                'message' => 'Izin berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'feedback' => 'nullable|string'
            ]);
            
            $permission = Permission::findOrFail($id);
            $permission->status = 'rejected';
            $permission->feedback = $request->feedback;
            $permission->save();
            
            // Hapus attendance jika ada (opsional, biarkan sebagai catatan)
            // $this->removeAttendance($permission);
            
            return response()->json([
                'success' => true,
                'message' => 'Izin ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============================================================
    // FUNGSI UNTUK MEMBUAT ATTENDANCE DARI PERMISSION
    // ============================================================
    private function createOrUpdateAttendance($permission)
    {
        try {
            // Cek apakah sudah ada attendance untuk tanggal ini
            $attendance = Attendance::where('user_id', $permission->user_id)
                ->where('date', $permission->date)
                ->first();
            
            $status = $permission->type === 'sick' ? 'sick' : 'permit';
            
            if ($attendance) {
                // Update status jika sudah ada
                $attendance->status = $status;
                $attendance->notes = 'Izin/Sakit: ' . $permission->reason;
                $attendance->save();
                
                \Log::info('Attendance updated for permission: ' . $permission->id);
            } else {
                // Buat attendance baru
                $user = User::find($permission->user_id);
                
                $newAttendance = new Attendance();
                $newAttendance->user_id = $permission->user_id;
                $newAttendance->date = $permission->date;
                $newAttendance->status = $status;
                $newAttendance->notes = 'Izin/Sakit: ' . $permission->reason;
                $newAttendance->is_valid_location = false; // Tidak perlu validasi lokasi
                $newAttendance->company_id = $user->company_id ?? 1;
                $newAttendance->save();
                
                \Log::info('Attendance created for permission: ' . $permission->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create attendance for permission: ' . $e->getMessage());
        }
    }
    
    // Opsional: Hapus attendance jika izin ditolak
    private function removeAttendance($permission)
    {
        try {
            $attendance = Attendance::where('user_id', $permission->user_id)
                ->where('date', $permission->date)
                ->whereIn('status', ['sick', 'permit'])
                ->first();
            
            if ($attendance) {
                $attendance->delete();
                \Log::info('Attendance removed for permission: ' . $permission->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to remove attendance: ' . $e->getMessage());
        }
    }
}