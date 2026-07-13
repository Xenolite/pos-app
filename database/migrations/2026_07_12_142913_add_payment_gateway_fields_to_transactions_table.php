<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // paid     : sudah dibayar (cash langsung dianggap paid, non-cash dari webhook Midtrans)
            // failed   : gagal/ditolak/dibatalkan/expired di Midtrans
            // (tidak ada status "pending" -- baris transaksi non-cash baru dibuat
            // setelah hasilnya final, lihat POSController::finalizeMidtransOrder())
            $table->string('payment_status')->default('paid')->after('payment_method');

            // Order ID unik yang dikirim ke Midtrans (harus unik per transaksi, beda dari id auto-increment)
            $table->string('midtrans_order_id')->nullable()->unique()->after('payment_status');

            // Snap token dari Midtrans, dipakai frontend untuk memunculkan popup Snap.js
            $table->string('snap_token')->nullable()->after('midtrans_order_id');

            // Tipe pembayaran aktual yang dipilih customer di Snap (qris, bank_transfer, gopay, dll)
            $table->string('payment_type')->nullable()->after('snap_token');

            // Waktu transaksi benar-benar settle/paid
            $table->timestamp('paid_at')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'midtrans_order_id',
                'snap_token',
                'payment_type',
                'paid_at',
            ]);
        });
    }
};
