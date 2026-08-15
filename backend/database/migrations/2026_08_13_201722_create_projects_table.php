<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 3)->default('IRR');

            $table->boolean('is_for_sale')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->enum('status', [
                'draft',
                'published',
                'archived',
            ])->default('draft');

            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
