<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom registration_status sudah ada
            if (!Schema::hasColumn('users', 'registration_status')) {
                $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('pending');
            }
            
            // Cek apakah kolom rejection_reason sudah ada
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            
            // Cek apakah kolom approved_by sudah ada
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users');
            }
            
            // Cek apakah kolom approved_at sudah ada
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            
            // Cek apakah kolom is_active sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'registration_status',
                'rejection_reason',
                'approved_by',
                'approved_at',
                'is_active'
            ]);
        });
    }
};