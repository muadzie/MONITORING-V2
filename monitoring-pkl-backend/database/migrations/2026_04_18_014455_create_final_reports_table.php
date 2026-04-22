// database/migrations/2026_04_18_000005_create_final_reports_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('final_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('abstract');
            $table->string('file_path');
            $table->string('file_name');
            $table->enum('status', ['draft', 'submitted', 'reviewing', 'approved', 'rejected'])->default('draft');
            $table->text('revision_notes')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('final_reports');
    }
};