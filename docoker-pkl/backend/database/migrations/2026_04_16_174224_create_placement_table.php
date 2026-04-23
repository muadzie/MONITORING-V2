// database/migrations/2024_01_01_000000_create_placement_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('company_id')->constrained('companies');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'canceled'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('placements');
    }
};