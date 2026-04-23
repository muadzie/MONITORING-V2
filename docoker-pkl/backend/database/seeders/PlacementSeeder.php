<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Placement;
use App\Models\User;
use App\Models\Company;

class PlacementSeeder extends Seeder
{
    public function run()
    {
        // Ambil siswa dan perusahaan yang sudah ada
        $students = User::where('role_id', 2)->get();
        $companies = Company::all();
        
        if ($students->count() > 0 && $companies->count() > 0) {
            Placement::create([
                'student_id' => $students[0]->id,
                'company_id' => $companies[0]->id,
                'start_date' => '2026-01-01',
                'end_date' => '2026-03-31',
                'status' => 'active',
                'notes' => 'Penempatan PKL semester genap'
            ]);
        }
    }
}