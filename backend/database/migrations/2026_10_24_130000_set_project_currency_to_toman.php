<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Published catalog prices are denominated in toman (IRT).
     * Project-request budgets remain quote-only and may use USDT.
     */
    public function up(): void
    {
        DB::table('projects')
            ->whereIn('currency', ['IRR', 'USD', 'EUR'])
            ->update(['currency' => 'IRT']);
    }

    public function down(): void
    {
        // The original currency per row cannot be reconstructed safely.
        // Intentionally do not revert currency labels and risk misrepresenting prices.
    }
};
