<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->timestamp('downloaded_at');

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'project_id']);
            $table->index('payment_id');
            $table->index('downloaded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
