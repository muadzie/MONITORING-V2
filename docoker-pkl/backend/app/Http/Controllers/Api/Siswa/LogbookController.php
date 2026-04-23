<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $logbooks = Logbook::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($logbooks);
    }

    public function store(Request $request)
    {
        $data = [
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'activity' => $request->activity,
            'description' => $request->description,
            'status' => 'pending'
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('logbooks', 'public');
            $data['attachment'] = $path;
        }

        $logbook = Logbook::create($data);
        return response()->json($logbook);
    }
}