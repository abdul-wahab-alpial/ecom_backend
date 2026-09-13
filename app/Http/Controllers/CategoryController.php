<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:180|unique:categories,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {

            $category = Category::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Category Added Successfully',
                'data' => $category,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $category->load('parent', 'children');
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => 'sometimes|required|string|max:180|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        DB::beginTransaction();

        try {

            $category = Category::findOrFail($id);

            $category->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Category Updated Successfully',
                'data' => $category,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to Update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $category = Category::findOrFail($id);

            $category->delete();

            DB::commit();

            return response()->json([
                'message' => 'Category deleted successfully',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
