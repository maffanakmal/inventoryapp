<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\TransactionItems;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionsExportExcel implements FromView, WithTitle
{
    protected $supplier;
    protected $dateFrom;
    protected $dateTo;
    protected $withItems;

    public function __construct($supplier = null, $dateFrom = null, $dateTo = null, $withItems = false)
    {
        $this->supplier = $supplier;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->withItems = $withItems;
    }

    public function view(): View
    {
        $query = Transaction::query()->orderByDesc('created_at');

        if ($this->supplier) {
            $query->where('supplier', 'like', '%' . $this->supplier . '%');
        }

        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween('created_at', [$this->dateFrom, $this->dateTo]);
        }

        $transactions = $query->get();

        $transactions->each(function ($t) {
            $t->items = TransactionItems::where('transaction_id', $t->transaction_id)->get();
        });

        return view('export.transaction-excel', [
            'transactions' => $transactions,
            'withItems' => $this->withItems
        ]);
    }

    public function title(): string
    {
        return 'Transaction History';
    }
}
