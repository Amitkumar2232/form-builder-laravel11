<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('form_submissions', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('data');
            }
            if (! Schema::hasColumn('form_submissions', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('form_submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->useCurrent()->after('user_agent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('form_submissions', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            if (Schema::hasColumn('form_submissions', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('form_submissions', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
        });
    }
};
