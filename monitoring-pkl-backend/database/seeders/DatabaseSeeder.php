<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // =============================================
        // 1. ROLES
        // =============================================
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Siswa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Guru', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Perusahaan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =============================================
        // 2. COMPANIES (Perusahaan Mitra PKL)
        // =============================================
        $companies = [
            [
                'id' => 1,
                'name' => 'PT. Honda Prospect Motor',
                'address' => 'Jl. Raya Cikarang, Bekasi',
                'latitude' => -6.2555,
                'longitude' => 107.1401,
                'radius' => 150,
                'phone' => '021-89891234',
                'email' => 'info@honda.co.id',
                'description' => 'Perusahaan manufaktur otomotif terkemuka',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'PT. Toyota Manufacturing Indonesia',
                'address' => 'Jl. Yos Sudarso, Sunter, Jakarta Utara',
                'latitude' => -6.1250,
                'longitude' => 106.8905,
                'radius' => 200,
                'phone' => '021-65308888',
                'email' => 'info@toyota.co.id',
                'description' => 'Perusahaan manufaktur otomotif',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'PT. Unilever Indonesia Tbk',
                'address' => 'Jl. Jenderal Sudirman Kav 15, Jakarta',
                'latitude' => -6.2150,
                'longitude' => 106.8025,
                'radius' => 100,
                'phone' => '021-52381234',
                'email' => 'info@unilever.co.id',
                'description' => 'Perusahaan consumer goods',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'PT. Pertamina (Persero)',
                'address' => 'Jl. Medan Merdeka Timur, Jakarta Pusat',
                'latitude' => -6.1750,
                'longitude' => 106.8225,
                'radius' => 250,
                'phone' => '021-3816111',
                'email' => 'info@pertamina.com',
                'description' => 'Perusahaan energi dan logistik',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'name' => 'PT. Bank Mandiri Tbk',
                'address' => 'Jl. Jenderal Gatot Subroto Kav 36-38, Jakarta',
                'latitude' => -6.2250,
                'longitude' => 106.8025,
                'radius' => 100,
                'phone' => '021-52997777',
                'email' => 'info@bankmandiri.co.id',
                'description' => 'Bank terbesar di Indonesia',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'name' => 'PT. Gojek Indonesia',
                'address' => 'Jl. Kemang Raya No 10, Jakarta Selatan',
                'latitude' => -6.2650,
                'longitude' => 106.8250,
                'radius' => 150,
                'phone' => '021-29035222',
                'email' => 'info@gojek.com',
                'description' => 'Perusahaan teknologi transportasi',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        DB::table('companies')->insert($companies);

        // =============================================
        // 3. CLASSES (Kelas)
        // =============================================
        $jurusan = [
            'Teknik Pemesinan' => ['TP 1', 'TP 2', 'TP 3'],
            'Desain Grafika' => ['DG 1', 'DG 2'],
            'Teknik Logistik' => ['TL 1', 'TL 2'],
            'Teknik dan Bisnis Sepeda Motor' => ['TBSM 1', 'TBSM 2', 'TBSM 3'],
            'Rekayasa Perangkat Lunak' => ['RPL 1', 'RPL 2', 'RPL 3'],
            'Teknik Komputer dan Jaringan' => ['TKJ 1', 'TKJ 2', 'TKJ 3'],
            'Bisnis Daring dan Pemasaran' => ['BDP 1', 'BDP 2'],
            'Otomatisasi dan Tata Kelola Perkantoran' => ['OTKP 1', 'OTKP 2'],
            'Akuntansi dan Keuangan Lembaga' => ['AKL 1', 'AKL 2'],
            'Tata Boga' => ['TB 1', 'TB 2'],
        ];

        $classes = [];
        $classId = 1;
        $tingkat = ['10', '11', '12'];
        
        foreach ($jurusan as $jurusanName => $kelasList) {
            foreach ($kelasList as $kelasName) {
                // Tentukan tingkat berdasarkan nama kelas
                $tingkatValue = substr($kelasName, 0, 2) == '10' ? '10' : (substr($kelasName, 0, 2) == '11' ? '11' : '12');
                
                $classes[] = [
                    'id' => $classId++,
                    'name' => $kelasName,
                    'jurusan' => $jurusanName,
                    'tingkat' => $tingkatValue,
                    'teacher_id' => null, // Akan diisi nanti
                    'academic_year' => '2025/2026',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        DB::table('classes')->insert($classes);

        // =============================================
        // 4. USERS - GURU
        // =============================================
        $gurus = [
            [
                'id' => 2,
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'budi.santoso@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'nip' => '197501012005011001',
                'mata_pelajaran' => 'Teknik Pemesinan',
                'phone' => '081234567891',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Siti Rahayu, S.Kom',
                'email' => 'siti.rahayu@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'nip' => '197803152006012002',
                'mata_pelajaran' => 'Rekayasa Perangkat Lunak',
                'phone' => '081234567892',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Ahmad Hidayat, S.T',
                'email' => 'ahmad.hidayat@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'nip' => '198005202007011003',
                'mata_pelajaran' => 'Teknik Komputer dan Jaringan',
                'phone' => '081234567893',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'name' => 'Dewi Kartika, S.E',
                'email' => 'dewi.kartika@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'nip' => '198307222008012004',
                'mata_pelajaran' => 'Akuntansi',
                'phone' => '081234567894',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'name' => 'Rina Susanti, S.Pd',
                'email' => 'rina.susanti@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'nip' => '198410102009012005',
                'mata_pelajaran' => 'Tata Boga',
                'phone' => '081234567895',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        DB::table('users')->insert($gurus);

        // Update teacher_id di classes
        DB::table('classes')->where('jurusan', 'Teknik Pemesinan')->update(['teacher_id' => 2]);
        DB::table('classes')->where('jurusan', 'Rekayasa Perangkat Lunak')->update(['teacher_id' => 3]);
        DB::table('classes')->where('jurusan', 'Teknik Komputer dan Jaringan')->update(['teacher_id' => 4]);
        DB::table('classes')->where('jurusan', 'Akuntansi dan Keuangan Lembaga')->update(['teacher_id' => 5]);
        DB::table('classes')->where('jurusan', 'Tata Boga')->update(['teacher_id' => 6]);

        // =============================================
        // 5. USERS - SISWA
        // =============================================
        $siswa = [];
        $siswaId = 10;
        
        // Data siswa per jurusan dan kelas
        $siswaData = [
            // Teknik Pemesinan - Kelas 12 TP 1
            ['name' => 'Ahmad Fauzi', 'nisn' => '0012345601', 'class_id' => 1, 'company_id' => 1],
            ['name' => 'Rizki Ramadhan', 'nisn' => '0012345602', 'class_id' => 1, 'company_id' => 1],
            ['name' => 'Bagas Saputra', 'nisn' => '0012345603', 'class_id' => 1, 'company_id' => 2],
            
            // Rekayasa Perangkat Lunak - Kelas 12 RPL 1
            ['name' => 'Siti Nurhaliza', 'nisn' => '0012345610', 'class_id' => 7, 'company_id' => 6],
            ['name' => 'Dewi Anggraeni', 'nisn' => '0012345611', 'class_id' => 7, 'company_id' => 6],
            ['name' => 'Muhammad Irfan', 'nisn' => '0012345612', 'class_id' => 7, 'company_id' => 6],
            
            // Teknik Komputer dan Jaringan - Kelas 12 TKJ 1
            ['name' => 'Andi Wijaya', 'nisn' => '0012345620', 'class_id' => 10, 'company_id' => 6],
            ['name' => 'Budi Prasetyo', 'nisn' => '0012345621', 'class_id' => 10, 'company_id' => 6],
            
            // Akuntansi - Kelas 12 AKL 1
            ['name' => 'Linda Febriyanti', 'nisn' => '0012345630', 'class_id' => 19, 'company_id' => 5],
            ['name' => 'Nadia Putri', 'nisn' => '0012345631', 'class_id' => 19, 'company_id' => 5],
            
            // Tata Boga - Kelas 12 TB 1
            ['name' => 'Putri Amelia', 'nisn' => '0012345640', 'class_id' => 21, 'company_id' => 3],
            ['name' => 'Rina Febriani', 'nisn' => '0012345641', 'class_id' => 21, 'company_id' => 3],
            
            // Desain Grafika - Kelas 12 DG 1
            ['name' => 'Eka Prasetya', 'nisn' => '0012345650', 'class_id' => 4, 'company_id' => 6],
            
            // Teknik Logistik - Kelas 12 TL 1
            ['name' => 'Fajar Nugroho', 'nisn' => '0012345660', 'class_id' => 6, 'company_id' => 4],
            
            // Bisnis Daring dan Pemasaran - Kelas 12 BDP 1
            ['name' => 'Gina Lestari', 'nisn' => '0012345670', 'class_id' => 13, 'company_id' => 5],
            
            // OTKP - Kelas 12 OTKP 1
            ['name' => 'Hendra Gunawan', 'nisn' => '0012345680', 'class_id' => 16, 'company_id' => 5],
        ];
        
        foreach ($siswaData as $data) {
            $siswa[] = [
                'id' => $siswaId++,
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '', $data['name'])) . '@student.smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'nisn' => $data['nisn'],
                'class_id' => $data['class_id'],
                'company_id' => $data['company_id'],
                'phone' => '0812' . rand(10000000, 99999999),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('users')->insert($siswa);

        // =============================================
        // 6. USERS - PERUSAHAAN
        // =============================================
        $perusahaanUsers = [
            [
                'id' => 100,
                'name' => 'PT. Honda Prospect Motor',
                'email' => 'honda@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 1,
                'phone' => '081234567800',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 101,
                'name' => 'PT. Toyota Manufacturing',
                'email' => 'toyota@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 2,
                'phone' => '081234567801',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 102,
                'name' => 'PT. Unilever Indonesia',
                'email' => 'unilever@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 3,
                'phone' => '081234567802',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 103,
                'name' => 'PT. Pertamina',
                'email' => 'pertamina@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 4,
                'phone' => '081234567803',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 104,
                'name' => 'PT. Bank Mandiri',
                'email' => 'mandiri@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 5,
                'phone' => '081234567804',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 105,
                'name' => 'PT. Gojek Indonesia',
                'email' => 'gojek@perusahaan.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'company_id' => 6,
                'phone' => '081234567805',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        DB::table('users')->insert($perusahaanUsers);

        // =============================================
        // 7. USERS - ADMIN (Sudah ada sebelumnya, pastikan)
        // =============================================
        $adminExists = DB::table('users')->where('email', 'admin@smkn1subang.sch.id')->first();
        if (!$adminExists) {
            DB::table('users')->insert([
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@smkn1subang.sch.id',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // =============================================
        // 8. PLACEMENTS (Penempatan PKL)
        // =============================================
        $students = DB::table('users')->where('role_id', 2)->get();
        foreach ($students as $student) {
            $exists = DB::table('placements')->where('student_id', $student->id)->exists();
            if (!$exists && $student->company_id) {
                DB::table('placements')->insert([
                    'student_id' => $student->id,
                    'company_id' => $student->company_id,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-06-30',
                    'status' => 'active',
                    'notes' => 'Penempatan PKL semester genap',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@smkn1subang.sch.id / password');
        $this->command->info('Guru: budi.santoso@smkn1subang.sch.id / password');
        $this->command->info('Siswa: ahmadfauzi@student.smkn1subang.sch.id / password');
        $this->command->info('Perusahaan: honda@perusahaan.com / password');
    }
}