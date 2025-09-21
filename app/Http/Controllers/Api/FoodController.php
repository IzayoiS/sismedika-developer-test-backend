<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Food::query();
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }
        $foods = $query->orderBy('name')->get();
        return response()->json([
            'data' => $foods
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $food = Food::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Food created successfully',
            'data' => $food
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Food $food)
    {
        return response()->json($food);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r, Food $food)
    {
        $data = $r->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $food->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Food updated successfully',
            'data' => $food
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        $food->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Food deleted successfully'
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $food = Food::findOrFail($id);

        $data = $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $food->update($data);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Food status updated successfully',
                'data' => $food
            ],
            200
        );
    }

    // app/Http/Controllers/Api/FoodController.php
    public function getCategories()
    {
        $categories = Food::distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();

        return response()->json($categories);
    }
}
