<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('placements', function (Blueprint $table) {
            if (!Schema::hasColumn('placements', 'shift_start')) {
                $table->string('shift_start', 5)->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->dropColumn('shift_start');
        });
    }
};
