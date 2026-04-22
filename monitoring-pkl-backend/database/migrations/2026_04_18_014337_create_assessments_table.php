// database/migrations/2026_04_18_000003_create_assessments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->enum('assessor_type', ['guru', 'perusahaan'])->default('guru');
            $table->integer('attendance_score')->nullable();      // nilai kehadiran
            $table->integer('logbook_score')->nullable();         // nilai logbook
            $table->integer('report_score')->nullable();          // nilai laporan
            $table->integer('attitude_score')->nullable();        // nilai sikap
            $table->integer('performance_score')->nullable();     // nilai kinerja
            $table->integer('discipline_score')->nullable();      // nilai kedisiplinan
            $table->decimal('final_score', 5, 2)->nullable();     // nilai akhir
            $table->text('notes')->nullable();                    // catatan
            $table->timestamps();
            
            // Index
            $table->index(['student_id', 'assessor_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
};