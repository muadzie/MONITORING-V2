<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Placement;
use Carbon\Carbon;

class AutoFillAttendance extends Command
{
    protected $signature = 'attendance:auto-fill {--date= : Tanggal yang akan diisi (YYYY-MM-DD)}';
    protected $description = 'Mengisi absensi alpha untuk siswa yang tidak hadir dan tidak memiliki izin';

    public function handle()
    {
        // Tentukan tanggal yang akan diproses
        if ($this->option('date')) {
            $date = $this->option('date');
        } else {
            $date = Carbon::yesterday()->format('Y-m-d');
        }
        
        $this->info('==========================================');
        $this->info('AUTO FILL ATTENDANCE - ALPHA');
        $this->info('Tanggal: ' . $date);
        $this->info('==========================================');
        
        // Ambil semua siswa aktif
        $students = User::where('role_id', 2)->where('is_active', 1)->get();
        
        $created = 0;
        $skipped = 0;
        
        foreach ($students as $student) {
            // Cek apakah sudah ada attendance untuk tanggal ini
            $attendance = Attendance::where('user_id', $student->id)
                ->where('date', $date)
                ->first();
            
            if ($attendance) {
                $skipped++;
                continue;
            }
            
            // Cek apakah siswa memiliki izin/sakit untuk tanggal ini
            $permission = Permission::where('user_id', $student->id)
                ->where('date', $date)
                ->where('status', 'approved')
                ->first();
            
            if ($permission) {
                // Buat attendance dengan status sick/permit
                $status = $permission->type === 'sick' ? 'sick' : 'permit';
                Attendance::create([
                    'user_id' => $student->id,
                    'company_id' => $student->company_id ?? 1,
                    'date' => $date,
                    'status' => $status,
                    'notes' => $permission->reason,
                    'is_valid_location' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $this->info("✓ {$student->name} - " . ($status === 'sick' ? 'SAKIT' : 'IZIN'));
                $created++;
                continue;
            }
            
            // Cek apakah siswa memiliki penempatan aktif
            $placement = Placement::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();
            
            if (!$placement) {
                $skipped++;
                continue;
            }
            
            // Buat attendance dengan status ALPHA
            Attendance::create([
                'user_id' => $student->id,
                'company_id' => $placement->company_id,
                'date' => $date,
                'status' => 'absent',
                'notes' => 'Tidak hadir tanpa keterangan',
                'is_valid_location' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $created++;
            $this->info("✗ {$student->name} - ALPHA (Tidak Hadir)");
        }
        
        $this->info('==========================================');
        $this->info("Total siswa: " . $students->count());
        $this->info("Dibuat: {$created} record");
        $this->info("Diskip: {$skipped} record");
        $this->info('==========================================');
    }
}