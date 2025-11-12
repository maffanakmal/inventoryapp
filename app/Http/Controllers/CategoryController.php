<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'inventoryApp | Categories',
        ];

        if ($request->ajax()) {
            $categories = Category::select('category_id', 'category_name')
                ->orderBy('category_id');

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('action', function ($category) {
                    return '
                            <button data-id="' . $category->category_id . '" class="btn btn-warning btn-sm" onclick="editCategory(this)">
                                <i class="fa-solid fa-pen me-1"></i> Edit 
                            </button>';
                })
                ->addColumn('checkbox', function ($category) {
                    return '<input type="checkbox" name="delete_selected[]" class="form-check-input delete-checkbox" value="' . $category->category_id . '">';
                })
                ->rawColumns(['action', 'checkbox'])
                ->make(true);
        }

        return view('admin.categories', $data);
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
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
        ], [
            'category_name.required' => 'Category name is required.',
            'category_name.string' => 'Category name must be a string.',
            'category_name.max' => 'Category name must not exceed 255 characters.',
            'category_name.unique' => 'Category name already exists.',
        ]);

        try {
            Category::create([
                'category_name' => $request->category_name,
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
            $category = Category::findOrFail($id);
            return response()->json([
                'status' => 200,
                'categories' => $category,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'icon' => 'error',
                'title' => 'Not Found',
                'message' => 'Category not found.',
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
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $id . ',category_id',
        ], [
            'category_name.required' => 'Category name is required.',
            'category_name.string' => 'Category name must be a string.',
            'category_name.max' => 'Category name must not exceed 255 characters.',
            'category_name.unique' => 'Category name already exists.',
        ]);

        try {
            $category = Category::findOrFail($id);

            // Assign new values
            $category->category_name = $request->category_name;

            // Check if any changes were made
            if (!$category->isDirty()) {
                return response()->json([
                    'status' => 200,
                    'icon' => 'info',
                    'title' => 'No Changes',
                    'message' => 'No changes detected. Category data is already up to date.',
                ]);
            }

            // Save the changes
            $category->save();

            return response()->json([
                'status' => 200,
                'icon' => 'success',
                'title' => 'Updated',
                'message' => 'Category updated successfully!',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'icon' => 'error',
                'title' => 'Not Found',
                'message' => 'Category not found.',
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
            $usedCategories = Category::whereIn('category_id', $ids)
                ->whereHas('products')
                ->pluck('category_name');

            if ($usedCategories->isNotEmpty()) {
                return response()->json([
                    'status'  => 400,
                    'icon'    => 'warning',
                    'title'   => 'Cannot Delete',
                    'message' => 'Some categories are still used by products: ' . $usedCategories->join(', '),
                ]);
            }

            Category::whereIn('category_id', $ids)->delete();

            return response()->json([
                'status'  => 200,
                'icon'    => 'success',
                'title'   => 'Deleted',
                'message' => 'Selected categories deleted successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status'  => 400,
                'icon'    => 'error',
                'title'   => 'Database Error',
                'message' => 'There was a problem deleting data from the database.',
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 500,
                'icon'    => 'error',
                'title'   => 'Server Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }
}
