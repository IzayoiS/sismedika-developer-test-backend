<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::orderBy('id')->get();
        return response()->json($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => 'in:available,occupied'
        ]);

        $table = Table::create([
            'name' => $data['name'],
            'status' => $data['status'] ?? 'available'
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Table created successfully',
                'data' => $table
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $table = Table::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Table retrieved successfully',
            'data' => $table
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function reserve($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status !== 'available') {
            return response()->json(['message' => 'Table cannot be reserved'], 400);
        }

        $table->update(['status' => 'reserved']);

        return response()->json([
            'status' => 'success',
            'message' => 'Table reserved',
            'table' => $table
        ]);
    }

    public function setInactive($id)
    {
        $table = Table::findOrFail($id);
        $table->update(['status' => 'inactive']);
        return response()->json([
            'status' => 'success',
            'message' => 'Table set to inactive',
            'table' => $table
        ]);
    }

}
