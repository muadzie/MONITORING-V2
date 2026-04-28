// database/migrations/2026_04_18_000002_add_photo_to_attendances_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'photo')) {
                $table->string('photo')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('attendances', 'is_valid_location')) {
                $table->boolean('is_valid_location')->default(false)->after('photo');
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['photo', 'is_valid_location']);
        });
    }
};