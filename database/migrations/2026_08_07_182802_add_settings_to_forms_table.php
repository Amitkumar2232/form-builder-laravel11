<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (! Schema::hasColumn('forms', 'settings')) {
                $table->json('settings')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (Schema::hasColumn('forms', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
