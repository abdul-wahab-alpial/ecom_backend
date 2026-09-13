<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
//for db transaction
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::get();
        return response()->json($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'slug' => 'required|string|max:180|unique:brands,slug',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);
            $brand = Brand::create($validated);
            DB::commit();
            return response()->json(
                [
                    'message' => 'Brand added successfully',
                    'data' => $brand,
                ],
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Failed to add brand',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'slug' => 'sometimes|required|string|max:180|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {

            $brand->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Brand updated successfully',
                'data' => $brand->fresh(),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        DB::beginTransaction();

        try {

            $brand->delete();

            DB::commit();

            return response()->json([
                'message' => 'Brand deleted successfully',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete brand',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
