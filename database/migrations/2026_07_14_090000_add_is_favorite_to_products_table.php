<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Disimpan sebagai smallint (bukan boolean bawaan Postgres), mengikuti
     * pola yang sama seperti is_active/price_after_tax — lihat
     * 2026_07_12_000000_convert_products_boolean_columns_to_smallint.php.
     * Cast 'boolean' di model Product tetap membuatnya terbaca true/false
     * di PHP, tanpa perlu workaround setAttribute apa pun.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->smallInteger('is_favorite')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_favorite');
        });
    }
};
