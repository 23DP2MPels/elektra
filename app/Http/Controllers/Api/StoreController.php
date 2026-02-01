<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $stores = Store::withCount('priceHistory')->get();
        } catch (\Throwable $e) {
            \Log::warning('StoreController::index: ' . $e->getMessage());
            $stores = collect([]);
        }

        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    public function show(Store $store): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'required|string|max:255',
            'url' => 'nullable|url',
            'logo_url' => 'nullable|url',
        ]);

        $store = Store::create($validated);

        return response()->json([
            'success' => true,
            'data' => $store,
            'message' => 'Store created successfully'
        ], 201);
    }

    public function update(Request $request, Store $store): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'sometimes|string|max:255',
            'url' => 'nullable|url',
            'logo_url' => 'nullable|url',
        ]);

        $store->update($validated);

        return response()->json([
            'success' => true,
            'data' => $store,
            'message' => 'Store updated successfully'
        ]);
    }

    public function destroy(Store $store): JsonResponse
    {
        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store deleted successfully'
        ]);
    }
}
