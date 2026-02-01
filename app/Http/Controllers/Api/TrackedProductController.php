<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrackedProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $trackedProducts = $user->trackedProducts()
            ->with(['category', 'specifications'])
            ->get();

        $trackedProducts->transform(function ($product) {
            $latestPrice = $product->priceHistory()->latest()->with('store')->first();
            if ($latestPrice) {
                $product->price = $latestPrice->cena;
                $product->store = $latestPrice->store;
            }
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $trackedProducts
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:preces,preces_id',
            'target_price' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();

        if ($user->trackedProducts()->where('sekotas_preces.preces_id', $validated['product_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already being tracked'
            ], 409);
        }

        $user->trackedProducts()->attach($validated['product_id'], [
            'target_price' => $validated['target_price'] ?? null
        ]);

        $product = Product::with(['category', 'specifications'])
            ->find($validated['product_id']);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product added to tracking list'
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'target_price' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();

        if (!$user->trackedProducts()->where('sekotas_preces.preces_id', $product->preces_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not being tracked'
            ], 404);
        }

        $user->trackedProducts()->updateExistingPivot($product->preces_id, [
            'target_price' => $validated['target_price'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking settings updated'
        ]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        $user->trackedProducts()->detach($product->preces_id);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from tracking list'
        ]);
    }
}
