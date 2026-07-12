<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected ?string $paymentMethod;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $paymentMethod = null, ?string $startDate = null, ?string $endDate = null)
    {
        $this->paymentMethod = $paymentMethod;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

  
    public function query()
    {
        return Transaction::query()
            ->with(['user', 'items.product'])
            ->when($this->paymentMethod, function ($query) {
                $query->where('payment_method', $this->paymentMethod);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate.' 00:00:00',
                    $this->endDate.' 23:59:59',
                ]);
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Kasir',
            'Produk',
            'Total',
            'Profit',
            'Service Charge',
            'Metode Pembayaran',
        ];
    }

    public function map($transaction): array
    {
        $productSummary = $transaction->items->map(function ($item) {
            return ($item->product->name ?? 'Produk Dihapus').' x'.$item->quantity;
        })->implode(', ');

        return [
            $transaction->id,
            $transaction->created_at->format('d-m-Y H:i'),
            $transaction->user->name ?? '-',
            $productSummary,
            $transaction->total,
            $transaction->profit,
            $transaction->service_charge ?? 0,
            $transaction->payment_method,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
