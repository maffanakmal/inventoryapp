<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Variants;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\HistoryStock;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TransactionItems;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExportExcel;
use App\Models\IncreasedPrices;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Transaction::query()->orderByDesc('created_at');

            if ($request->supplier) {
                $query->where('supplier', 'like', '%' . $request->supplier . '%');
            }

            if ($request->date_from && $request->date_to) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('total_price', fn($row) => 'Rp ' . number_format($row->total_price, 0, ',', '.'))
                ->addColumn('transaction_date', fn($row) => $row->created_at->format('d M Y'))
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('transactions.detail', $row->transaction_id) . '" class="btn btn-info btn-sm">
                            <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.transaction-history');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            if ($request->ajax()) {
                $variants = Variants::select(['variant_id', 'sku', 'variant_name'])->get();

                return response()->json([
                    'status' => 200,
                    'variants' => $variants,
                ]);
            }

            return view('admin.transactions');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    "status" => 500,
                    "title" => "Internal Server Error",
                    "message" => $e->getMessage(),
                    "icon" => "error"
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier' => 'required|string|max:255',
                'contact' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.variant_id' => 'required|integer|exists:variants,variant_id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.batch_number' => 'nullable|string|max:255',
            ]);

            $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

            if (!$items || !is_array($items) || count($items) === 0) {
                return response()->json([
                    'status' => 422,
                    'icon' => 'warning',
                    'title' => 'Invalid Data',
                    'message' => 'At least one item must be provided.',
                ], 422);
            }

            $totalItems = collect($items)->sum('quantity');
            $totalPrice = collect($items)->sum(fn($i) => $i['quantity'] * $i['price']);

            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . now()->format('YmdHis'),
                'transaction_type' => 'in',
                'total_items' => $totalItems,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
                'officer' => 'Administrator',
                'supplier' => $request->supplier,
                'customer' => null,
                'contact' => $request->contact,
            ]);

            foreach ($items as $item) {
                $variant = Variants::with('product')->find($item['variant_id']);

                $oldPrice = $variant->variant_price;
                $newPrice = $item['price'];

                $transactionItem = TransactionItems::create([
                    'transaction_id' => $transaction->transaction_id,
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->variant_id,
                    'product_name_snapshot' => $variant->product->product_name ?? 'Unknown Product',
                    'variant_name_snapshot' => $variant->variant_name ?? 'Unknown Variant',
                    'sku_snapshot' => $variant->sku ?? '-',
                    'unit_price_snapshot' => $newPrice,
                    'batch_number' => $item['batch_number'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $newPrice,
                    'total' => $item['quantity'] * $newPrice,
                ]);

                if ($newPrice > $oldPrice) {
                    IncreasedPrices::create([
                        'variant_id' => $variant->variant_id,
                        'transaction_id' => $transaction->transaction_id,
                        'transaction_item_id' => $transactionItem->transaction_item_id,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'increase_amount' => $newPrice - $oldPrice,
                        'is_confirmed' => false,
                    ]);
                }

                $variant->increment('stock_quantity', $item['quantity']);
                $updatedStock = $variant->stock_quantity;

                HistoryStock::create([
                    'variant_id' => $variant->variant_id,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => 'in',
                    'input_quantity' => $item['quantity'],
                    'output_quantity' => 0,
                    'balance_quantity' => $updatedStock,
                    'officer' => 'Administrator',
                    'notes' => 'Stock added from transaction ' . $transaction->transaction_code,
                ]);
            }

            return response()->json([
                'status' => 200,
                'icon' => 'success',
                'title' => 'Transaction Saved',
                'message' => 'Inbound transaction for supplier "' . $request->supplier . '" saved successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function supplier()
    {
        try {
            $supplier = Transaction::distinct()
                ->pluck('supplier')
                ->filter()
                ->values();

            return response()->json([
                'status' => 200,
                'supplier' => $supplier,
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => 500,
                "title" => "Internal Server Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ], 500);
        }
    }

    public function detail(Request $request, $id)
    {
        try {

            $transaction = Transaction::findOrFail($id);
            $items = TransactionItems::where('transaction_id', $id)->get();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 200,
                    'transaction' => [
                        'transaction_code' => $transaction->transaction_code,
                        'supplier' => $transaction->supplier,
                        'contact' => $transaction->contact,
                        'total_items' => $transaction->total_items,
                        'total_price' => 'Rp ' . number_format($transaction->total_price, 0, ',', '.'),
                        'notes' => $transaction->notes,
                        'officer' => $transaction->officer,
                        'created_at_formatted' => $transaction->created_at->format('d M Y H:i'),
                    ],
                    'items' => $items->map(function ($item, $index) {
                        return [
                            'no' => $index + 1,
                            'variant_name' => $item->variant_name_snapshot,
                            'batch_number' => $item->batch_number ?? '-',
                            'quantity' => $item->quantity,
                            'price' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                            'subtotal' => 'Rp ' . number_format($item->quantity * $item->price, 0, ',', '.'),
                        ];
                    }),
                ]);
            }

            return view('admin.transaction-detail', ['transactionId' => $id]);
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    "status" => 500,
                    "title" => "Internal Server Error",
                    "message" => $e->getMessage(),
                    "icon" => "error"
                ], 500);
            }

            return back()->with('error', 'Fail to load detail transaction: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $supplier = $request->input('supplier');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $withItems = $request->has('downloadWithItems');

        return Excel::download(
            new TransactionsExportExcel($supplier, $dateFrom, $dateTo, $withItems),
            'transaction_history_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf($id)
    {
        try {
            $transaction = Transaction::with('transactionItems')->findOrFail($id);

            $pdf = Pdf::loadView('export.transaction-pdf', [
                'transaction' => $transaction,
                'items' => $transaction->transactionItems,
            ])->setPaper('a4', 'portrait');

            $fileName = 'Transaction_' . $transaction->transaction_code . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Export Failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
