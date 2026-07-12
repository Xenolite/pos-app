<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat Snap token untuk sebuah transaksi.
     *
     * @param  string  $orderId
     * @param  int  $grossAmount
     * @param  array  $itemDetails
     * @param  array  $customerDetails
     * @return string  snap token
     */
    public function createSnapToken(string $orderId, int $grossAmount, array $itemDetails = [], array $customerDetails = []): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            // Batasi metode yang muncul di Snap supaya sesuai kebutuhan POS
            'enabled_payments' => [
                'qris', 'gopay', 'shopeepay',
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Ambil & validasi notifikasi (webhook) dari Midtrans.
     */
    public function handleNotification(): Notification
    {
        return new Notification();
    }
}
