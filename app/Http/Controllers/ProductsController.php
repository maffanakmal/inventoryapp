<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use App\Models\Products;
use App\Models\TransactionItems;
use App\Models\Variants;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'inventoryApp | Products',
        ];

        if ($request->ajax()) {
            $products = Products::with('category:category_id,category_name')
                ->select('product_id', 'product_name', 'category_id', 'product_description')
                ->orderBy('product_id');

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('category_name', function ($product) {
                    return $product->category ? $product->category->category_name : '-';
                })
                ->addColumn('action', function ($product) {
                    return '
                <button data-id="' . $product->product_id . '" class="btn btn-warning btn-sm" onclick="editProduct(this)">
                    Edit
                </button>
                <a href="' . route('master-data.variants', $product->product_id) . '" class="btn btn-info btn-sm">
                    Detail
                </a>';
                })
                ->addColumn('checkbox', function ($product) {
                    return '<input type="checkbox" name="delete_selected[]" class="form-check-input delete-checkbox" value="' . $product->product_id . '">';
                })
                ->rawColumns(['action', 'checkbox'])
                ->make(true);
        }


        return view('admin.products', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'product_description' => 'required|string',
        ], [
            'product_name.required' => 'Product name is required.',
            'product_name.string' => 'Product name must be a string.',
            'product_name.max' => 'Product name must not exceed 255 characters.',
            'product_name.unique' => 'Product name already exists.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'product_description.required' => 'Product description is required.',
            'product_description.string' => 'Product description must be a string.',
        ]);

        try {
            Products::create([
                'product_name' => $request->product_name,
                'category_id' => $request->category_id,
                'product_description' => $request->product_description,
            ]);

            return response()->json([
                'status' => 200,
                'icon' => 'success',
                'title' => 'Success',
                'message' => 'Category created successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 400,
                'icon' => 'error',
                'title' => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $products = Products::findOrFail($id);

            return response()->json([
                'status' => 200,
                'products' => $products,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'icon' => 'error',
                'title' => 'Not Found',
                'message' => 'Product not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'product_name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,category_id',
                'product_description' => 'required|string',
            ],
            [
                'product_name.required' => 'Product name is required.',
                'product_name.string' => 'Product name must be a string.',
                'product_name.max' => 'Product name must not exceed 255 characters.',
                'product_name.unique' => 'Product name already exists.',
                'category_id.required' => 'Category is required.',
                'category_id.exists' => 'Selected category does not exist.',
                'product_description.required' => 'Product description is required.',
                'product_description.string' => 'Product description must be a string.',
            ]
        );

        try {
            $product = Products::findOrFail($id);

            $product->product_name = $request->product_name;
            $product->category_id = $request->category_id;
            $product->product_description = $request->product_description;

            if (!$product->isDirty()) {
                return response()->json([
                    'status' => 200,
                    'icon' => 'info',
                    'title' => 'No Changes',
                    'message' => 'No changes detected. Product data is already up to date.',
                ]);
            }

            // Save the changes
            $product->save();

            return response()->json([
                'status' => 200,
                'icon' => 'success',
                'title' => 'Updated',
                'message' => 'Product updated successfully!',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'icon' => 'error',
                'title' => 'Not Found',
                'message' => 'Product not found.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 400,
                'icon' => 'error',
                'title' => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function selectedDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        try {
            $hasRelations = Variants::whereIn('product_id', $ids)->exists() ||
                TransactionItems::whereIn('product_id', $ids)->exists();

            if ($hasRelations) {
                return response()->json([
                    'status' => 400,
                    'icon' => 'warning',
                    'title' => 'Cannot Delete',
                    'message' => 'One or more selected products have related variants or transaction items and cannot be deleted.',
                ]);
            }

            Products::whereIn('product_id', $ids)->delete();

            return response()->json([
                'status' => 200,
                'icon' => 'success',
                'title' => 'Deleted',
                'message' => 'Selected products deleted successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 400,
                'icon' => 'error',
                'title' => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'icon' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
