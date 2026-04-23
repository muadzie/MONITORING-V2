<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Untuk guru
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable()->after('nisn');
            }
            if (!Schema::hasColumn('users', 'mata_pelajaran')) {
                $table->string('mata_pelajaran')->nullable()->after('nip');
            }
            
            // Untuk siswa
            if (!Schema::hasColumn('users', 'kelas')) {
                $table->string('kelas')->nullable()->after('nisn');
            }
            if (!Schema::hasColumn('users', 'jurusan')) {
                $table->string('jurusan')->nullable()->after('kelas');
            }
            
            // Untuk perusahaan
            if (!Schema::hasColumn('users', 'bidang_usaha')) {
                $table->string('bidang_usaha')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'kontak_person')) {
                $table->string('kontak_person')->nullable()->after('bidang_usaha');
            }
            
            // Index untuk performa
            $table->index('nip');
            $table->index('nisn');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip', 'mata_pelajaran', 'kelas', 'jurusan', 
                'bidang_usaha', 'kontak_person'
            ]);
        });
    }
};