<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('placements', function (Blueprint $table) {
            if (!Schema::hasColumn('placements', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};