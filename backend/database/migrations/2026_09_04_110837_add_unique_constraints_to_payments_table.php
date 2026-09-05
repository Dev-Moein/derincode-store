<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(
                'payments_authority_index'
            );

            $table->dropIndex(
                'payments_gateway_transaction_id_index'
            );

            $table->unique(
                'authority',
                'payments_authority_unique'
            );

            $table->unique(
                ['gateway', 'transaction_id'],
                'payments_gateway_transaction_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(
                'payments_authority_unique'
            );

            $table->dropUnique(
                'payments_gateway_transaction_id_unique'
            );

            $table->index(
                'authority',
                'payments_authority_index'
            );

            $table->index(
                ['gateway', 'transaction_id'],
                'payments_gateway_transaction_id_index'
            );
        });
    }
};
