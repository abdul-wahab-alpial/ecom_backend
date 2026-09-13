<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::with('state')->get();

        return response()->json($cities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cities')->where(fn ($query) => $query->where('state_id', $request->state_id)),
            ],
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $city = City::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'City added successfully',
                'data' => $city,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        $city->load('state');

        return response()->json($city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'state_id' => 'sometimes|required|exists:states,id',
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('cities')->ignore($city->id)->where(fn ($query) => $query->where('state_id', $request->state_id ?? $city->state_id)),
            ],
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $city->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'City updated successfully',
                'data' => $city->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        DB::beginTransaction();

        try {
            $city->delete();

            DB::commit();

            return response()->json([
                'message' => 'City deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
