<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IRR');

            $table->string('gateway', 50);
            $table->string('transaction_id')->nullable();

            $table->enum('status', [
                'pending',
                'successful',
                'failed',
                'cancelled',
                'refunded',
            ])->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['gateway', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
