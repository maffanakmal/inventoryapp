<?php

namespace App\Http\Controllers;

use App\Models\HistoryStock;
use Illuminate\Http\Request;

class HistoryStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        if ($request->ajax()) {
            $historyStocks = HistoryStock::where('variant_id', $id)
                ->with(['transaction', 'variant'])
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($historyStocks)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('transaction_code', function ($row) {
                    return $row->transaction->transaction_code ?? '-';
                })
                ->addColumn('transaction_type', function ($row) {
                    return ucfirst($row->transaction_type);
                })
                ->addColumn('input_qty', function ($row) {
                    return $row->input_quantity;
                })
                ->addColumn('output_qty', function ($row) {
                    return $row->output_quantity;
                })
                ->addColumn('balance_qty', function ($row) {
                    return $row->balance_quantity;
                })
                ->addColumn('officer', function ($row) {
                    return $row->officer ?? '-';
                })
                ->make(true);
        }

        $data = [
            'variant_id' => $id,
        ];

        return view('admin.stock-history', $data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HistoryStock $historyStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HistoryStock $historyStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HistoryStock $historyStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HistoryStock $historyStock)
    {
        //
    }
}
