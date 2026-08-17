<?php

use App\Enums\ProjectRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description');

            $table->decimal('budget', 15, 2)
                ->nullable();

            $table->string('currency', 3)
                ->default('USD');

            $table->string('status')
                ->default(ProjectRequestStatus::PENDING->value);

            $table->text('admin_note')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
