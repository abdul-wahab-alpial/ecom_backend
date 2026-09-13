<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $states = State::with('country')->get();

        return response()->json($states);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('states')->where(fn ($query) => $query->where('country_id', $request->country_id)),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('states')->where(fn ($query) => $query->where('country_id', $request->country_id)),
            ],
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $state = State::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'State added successfully',
                'data' => $state,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to add state',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        $state->load('country');

        return response()->json($state);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'country_id' => 'sometimes|required|exists:countries,id',
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('states')->ignore($state->id)->where(fn ($query) => $query->where('country_id', $request->country_id ?? $state->country_id)),
            ],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('states')->ignore($state->id)->where(fn ($query) => $query->where('country_id', $request->country_id ?? $state->country_id)),
            ],
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $state->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'State updated successfully',
                'data' => $state->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update state',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        DB::beginTransaction();

        try {
            $state->delete();

            DB::commit();

            return response()->json([
                'message' => 'State deleted successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete state',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
