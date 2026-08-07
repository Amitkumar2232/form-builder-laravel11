<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (! Schema::hasColumn('forms', 'uuid')) {
                $table->string('uuid')->nullable()->after('version');
            }
        });

        DB::table('forms')->whereNull('uuid')->get(['id'])->each(function ($row) {
            DB::table('forms')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

        $hasUuidIndex = collect(DB::select("SHOW INDEX FROM forms WHERE Key_name = 'forms_uuid_unique'"))->isNotEmpty();

        Schema::table('forms', function (Blueprint $table) use ($hasUuidIndex) {
            if (! Schema::hasColumn('forms', 'uuid')) {
                return;
            }

            if (! $hasUuidIndex) {
                $table->unique('uuid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (Schema::hasColumn('forms', 'uuid')) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            }
        });
    }
};
