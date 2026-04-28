<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'jurusan')) {
                $table->string('jurusan')->nullable()->after('name');
            }
            if (!Schema::hasColumn('classes', 'tingkat')) {
                $table->string('tingkat')->nullable()->after('jurusan');
            }
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['jurusan', 'tingkat']);
        });
    }
};