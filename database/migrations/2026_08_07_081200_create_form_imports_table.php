<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_imports', function (Blueprint $table) {
            $table->id();
            $table->uuid('import_uuid')->unique();
            $table->foreignId('form_id')->nullable()->constrained()->nullOnDelete();
            $table->string('original_filename');
            $table->string('file_type'); // docx, xlsx
            $table->string('stored_path');
            $table->enum('status', ['pending', 'processing', 'preview', 'completed', 'failed'])->default('pending');
            $table->json('parsed_schema')->nullable();
            $table->json('mapping_overrides')->nullable();
            $table->json('warnings')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_imports');
    }
};
