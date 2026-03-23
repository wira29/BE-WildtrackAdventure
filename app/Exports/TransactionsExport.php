<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{

protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    public function collection()
    {
        $query = Transaction::query();

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('package_name', 'like', '%' . $search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Nama Customer',
            'Email',
            'No. HP',
            'KTP',
            'Participants',
            'Jumlah Pax',
            'Paket',
            'Total Harga',
            'Metode Pembayaran',
            'Status',
            'Tanggal Transaksi',
        ];
    }

    public function map($transaction): array
{
    // Logic untuk participants
    $decoded = [];
    if (is_array($transaction->participants)) {
        $decoded = $transaction->participants;
    } elseif (is_string($transaction->participants)) {
        $decoded = json_decode($transaction->participants, true) ?? [];
    }

    $participantsString = implode(', ', $decoded);

    return [
        $transaction->order_id,
        $transaction->name,
        $transaction->email,
        $transaction->phone,
        $transaction->image ? asset('storage/' . $transaction->image) : '-',
        $participantsString ?: '-',
        count($decoded), // Lebih aman daripada count(json_decode(...))
        $transaction->package_name,
        $transaction->total,
        $transaction->payment_method,
        $transaction->status,
        // Cek jika created_at ada sebelum diformat
        $transaction->created_at ? $transaction->created_at->format('d-m-Y H:i') : '-',
    ];
}

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
