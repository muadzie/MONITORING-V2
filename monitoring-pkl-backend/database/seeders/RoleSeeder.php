<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Administrator dengan akses penuh'],
            ['name' => 'Guru', 'description' => 'Guru pembimbing PKL'],
            ['name' => 'Siswa', 'description' => 'Siswa peserta PKL'],
            ['name' => 'Perusahaan', 'description' => 'Pembimbing dari perusahaan'],
        ];
        
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}