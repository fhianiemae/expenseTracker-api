<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all categories that belong to the logged-in user
        return $request->user()->categories()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        // Create category under the logged-in user
        $category = $request->user()->categories()->create($data);

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Category $category)
    {
        $this->authorizeCategory($request, $category);
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($request, $category);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:income,expense',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        $this->authorizeCategory($request, $category);

        $category->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Ownership check
     */
    protected function authorizeCategory(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
    }
}
