<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::get();

        return response()->json($permissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);

        DB::beginTransaction();

        try {
            $permission = Permission::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Permission created successfully',
                'data' => $permission,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:permissions,name,' . $permission->id,
        ]);

        DB::beginTransaction();

        try {
            $permission->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Permission updated successfully',
                'data' => $permission->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        DB::beginTransaction();

        try {
            $permission->delete();

            DB::commit();

            return response()->json([
                'message' => 'Permission deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
