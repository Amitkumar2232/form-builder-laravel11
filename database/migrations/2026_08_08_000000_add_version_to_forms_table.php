<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (! Schema::hasColumn('forms', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (Schema::hasColumn('forms', 'version')) {
                $table->dropColumn('version');
            }
        });
    }
};
