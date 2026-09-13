<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::with(['company', 'city'])->get();

        return response()->json($branches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:150',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches')->where(fn ($query) => $query->where('company_id', $request->company_id)),
            ],
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('branches')->where(fn ($query) => $query->where('company_id', $request->company_id)),
            ],
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $branch = Branch::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Branch added successfully',
                'data' => $branch,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        $branch->load(['company', 'city']);

        return response()->json($branch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'city_id' => 'sometimes|required|exists:cities,id',
            'name' => 'sometimes|required|string|max:150',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('branches')->ignore($branch->id)->where(fn ($query) => $query->where('company_id', $request->company_id ?? $branch->company_id)),
            ],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:180',
                Rule::unique('branches')->ignore($branch->id)->where(fn ($query) => $query->where('company_id', $request->company_id ?? $branch->company_id)),
            ],
            'email' => 'sometimes|required|email|max:150',
            'phone' => 'sometimes|required|string|max:30',
            'address' => 'sometimes|required|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $branch->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Branch updated successfully',
                'data' => $branch->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        DB::beginTransaction();

        try {
            $branch->delete();

            DB::commit();

            return response()->json([
                'message' => 'Branch deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete branch',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
