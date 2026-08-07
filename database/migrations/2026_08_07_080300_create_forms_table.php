<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('schema');
            $table->json('settings')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_template')->default(false);
            $table->string('template_category')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('is_template');
            $table->index(['status', 'created_at']);
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
