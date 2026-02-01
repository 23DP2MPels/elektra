<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $categories = Category::with('parent')->withCount('products')->get();
        } catch (\Throwable $e) {
            \Log::warning('CategoryController::index: ' . $e->getMessage());
            $categories = collect([]);
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['parent', 'children', 'products']);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'required|string|max:255',
            'apraksts' => 'nullable|string',
            'vecaka_kategorijas_id' => 'nullable|exists:kategorijas,kategorijas_id',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'sometimes|string|max:255',
            'apraksts' => 'nullable|string',
            'vecaka_kategorijas_id' => 'nullable|exists:kategorijas,kategorijas_id',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
