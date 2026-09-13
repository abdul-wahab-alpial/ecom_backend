<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index()
    {
        $country = Country::get();
        return response()->json($country);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:3|unique:countries,code',
            'is_active' => 'boolean',
        ]);
        DB::beginTransaction();
        try {
            $country = Country::create($validated);
            DB::commit();
            return response()->json([
                'message' => 'Country listed successfully',
                'data' => $country,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unable to list Country',
                'error' => $e->getMessage(),
            ], 500);
        }

    }
    public function show(string $id)
    {
        $country = Country::findOrFail($id);
        return response()->json($country);

    }
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:3|unique:countries,code',
            'is_active' => 'boolean',
        ]);
        DB::beginTransaction();
        try {
            $country = Country::findOrFail($id);
            $country->update($validated);
            DB::commit();
            return response()->json([
                'message' => 'Country has been updated',
                'data' => $country->fresh(),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to update Country',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
   
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $country = Country::findOrFail($id);

            $country->delete();

            DB::commit();

            return response()->json([
                'message' => 'Country deleted successfully',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete Country',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

