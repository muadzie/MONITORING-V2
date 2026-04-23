<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function students(Request $request)
    {
        $students = User::where('teacher_id', $request->user()->id)
            ->where('role_id', 2)
            ->with('company')
            ->get();

        foreach ($students as $student) {
            $today = Attendance::where('user_id', $student->id)->whereDate('date', Carbon::today())->first();
            $student->today_status = $today ? $today->status : 'absent';
            $student->logbook_count = Logbook::where('user_id', $student->id)->count();
        }

        return response()->json($students);
    }
}