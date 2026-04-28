<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class CheckAlphaAttendance extends Command
{
    protected $signature = 'attendance:check-alpha';
    protected $description = 'Check and mark absent students as alpha';

    public function handle()
    {
        $today = Carbon::today();
        $students = User::students()->get();

        foreach ($students as $student) {
            $attendance = Attendance::where('user_id', $student->id)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'user_id' => $student->id,
                    'company_id' => $student->company_id,
                    'date' => $today,
                    'status' => 'absent',
                    'notes' => 'Otomatis alpha (tidak absen)'
                ]);
                $this->info("Marked student {$student->name} as alpha");
            }
        }
    }
}
