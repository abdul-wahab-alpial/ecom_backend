<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::get();

        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Role created successfully',
                'data' => $role,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:roles,name,' . $role->id,
            'is_active' => 'sometimes|required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $role->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Role updated successfully',
                'data' => $role->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        DB::beginTransaction();

        try {
            $role->delete();

            DB::commit();

            return response()->json([
                'message' => 'Role deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //assig role permissions
    public function assignPermissions(){
        
    }
}
