<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use App\Models\Variants;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Variants::with([
                'product:product_id,product_name,category_id',
                'product.category:category_id,category_name'
            ]);

            if ($request->category_id) {
                $query->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            if ($request->stock) {
                $range = explode('-', $request->stock);

                $min = (int) $range[0];
                $max = isset($range[1]) ? (int) $range[1] : null;

                if ($max) {
                    $query->whereBetween('stock_quantity', [$min, $max]);
                } else {
                    $query->where('stock_quantity', '>=', $min);
                }
            }

            $variants = $query->select('variant_id', 'product_id', 'sku', 'variant_name', 'variant_price', 'stock_quantity');

            return datatables()->of($variants)
                ->addIndexColumn()
                ->addColumn('sku', function ($variant) {
                    return $variant ? $variant->sku : '-';
                })
                ->addColumn('variant_name', function ($variant) {
                    return $variant ? $variant->variant_name : '-';
                })
                ->addColumn('category_name', function ($variant) {
                    return $variant->product && $variant->product->category
                        ? $variant->product->category->category_name
                        : '-';
                })
                ->addColumn('variant_price', function ($variant) {
                    return 'Rp ' . number_format($variant->variant_price, 0, ',', '.');
                })
                ->addColumn('action', function ($variant) {
                    return '<a href="' . route('master-data.stocks.history', $variant->variant_id) . '" class="btn btn-info btn-sm">
                            Detail
                            </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = [
            'title' => 'InventoryApp | Product Stock',
        ];

        return view('admin.product-stock', $data);
    }

    public function create()
    {
        try {
            $category = Category::select(['category_id', 'category_name'])->get();

            return response()->json([
                'status' => 200,
                'category' => $category,
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
}
