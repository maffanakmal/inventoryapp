<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Products;
use App\Models\Variants;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VariantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $product = Products::with('variants')->where('product_id', $id)->firstOrFail();

        return view('admin.variants', compact('product'));
    }

    public function variantList(Request $request, $id)
    {
        $search = $request->get('search');
        $limit = $request->get('limit', 6);

        $query = Variants::where('product_id', $id);

        if (!empty($search)) {
            $query->where('variant_name', 'like', '%' . $search . '%');
        }

        $variants = $query->paginate($limit);

        $html = view('partials.variant-list', compact('variants'))->render();

        return response()->json([
            'html' => $html,
            'total' => $variants->total(),
            'from' => $variants->firstItem(),
            'to' => $variants->lastItem(),
            'last_page' => $variants->lastPage(),
            'current_page' => $variants->currentPage(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,product_id',
            'variant_name'   => 'required|string|max:255|unique:variants,variant_name',
            'variant_price'  => 'required|numeric|min:0',
            'variant_stock'  => 'required|integer|min:0',
            'variant_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'product_id.required' => 'Product ID is required.',
            'variant_name.required'  => 'Variant name is required.',
            'variant_price.required' => 'Variant price is required.',
            'variant_stock.required' => 'Variant stock is required.',
            'variant_image.image'    => 'File must be an image.',
            'variant_image.mimes'    => 'Image must be jpg, jpeg, or png.',
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('variant_image')) {
                $image = $request->file('variant_image');
                $path = $image->store('variants', 'public');
                $imagePath = $path;
            }

            $sku = strtoupper(Str::slug($request->variant_name, '-')) . '-' . Str::upper(Str::random(5));

            Variants::create([
                'product_id'     => $request->product_id,
                'sku'            => $sku,
                'variant_name'   => $request->variant_name,
                'variant_image'  => $imagePath,
                'variant_price'  => $request->variant_price,
                'stock_quantity' => $request->variant_stock,
            ]);

            return response()->json([
                'status'  => 200,
                'icon'    => 'success',
                'title'   => 'Success',
                'message' => 'Variant created successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status'  => 400,
                'icon'    => 'error',
                'title'   => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'icon'    => 'error',
                'title'   => 'Server Error',
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
            $variant = Variants::findOrFail($id);

            return response()->json([
                'status' => 200,
                'variant' => $variant,
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
    public function edit(Variants $variant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'variant_name'   => 'required|string|max:255|unique:variants,variant_name,' . $id . ',variant_id',
            'variant_price'  => 'required|numeric|min:0',
            'variant_stock'  => 'required|integer|min:0',
            'variant_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'variant_name.required'  => 'Variant name is required.',
            'variant_price.required' => 'Variant price is required.',
            'variant_stock.required' => 'Variant stock is required.',
            'variant_image.image'    => 'File must be an image.',
            'variant_image.mimes'    => 'Image must be jpg, jpeg, or png.',
        ]);

        try {
            $variant = Variants::findOrFail($id);

            $original = $variant->getOriginal();

            if ($request->hasFile('variant_image')) {
                if ($variant->variant_image && Storage::exists('public/' . $variant->variant_image)) {
                    Storage::delete('public/' . $variant->variant_image);
                }

                $image = $request->file('variant_image');
                $path = $image->store('variants', 'public');
                $variant->variant_image = $path;
            }

            $variant->variant_name   = $request->variant_name;
            $variant->variant_price  = $request->variant_price;
            $variant->stock_quantity = $request->variant_stock;

            if (!$request->hasFile('variant_image') && !$variant->isDirty()) {
                return response()->json([
                    'status' => 200,
                    'icon' => 'info',
                    'title' => 'No Changes',
                    'message' => 'No changes detected. Product data is already up to date.',
                ]);
            }

            $variant->save();

            return response()->json([
                'status'  => 200,
                'icon'    => 'success',
                'title'   => 'Updated',
                'message' => 'Variant updated successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status'  => 400,
                'icon'    => 'error',
                'title'   => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'icon'    => 'error',
                'title'   => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $variant = Variants::findOrFail($id);

            if ($variant->variant_image && Storage::exists('public/' . $variant->variant_image)) {
                Storage::delete('public/' . $variant->variant_image);
            }

            $variant->delete();

            return response()->json([
                'status'  => 200,
                'icon'    => 'success',
                'title'   => 'Deleted',
                'message' => 'Variant deleted successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status'  => 400,
                'icon'    => 'error',
                'title'   => 'Database Error',
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'icon'    => 'error',
                'title'   => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
