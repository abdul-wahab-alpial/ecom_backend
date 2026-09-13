<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::get();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:180|unique:companies,slug',
            'description' => 'nullable|string',
            'email' => 'required|email|max:150',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $company = Company::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Company created successfully',
                'data' => $company,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:180|unique:companies,slug',
            'description' => 'nullable|string',
            'email' => 'required|email|max:150',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $company = Company::findOrFail($id);
            $company->update($validated);
            DB::commit();
            return response()->json([
                'message' => 'Company has been updated',
                'data' => $company->fresh(),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to update Company',
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

            $company = Company::findOrFail($id);

            $company->delete();

            DB::commit();

            return response()->json([
                'message' => 'Company deleted successfully',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
