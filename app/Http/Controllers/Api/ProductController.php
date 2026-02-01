<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'specifications'])
            ->withCount('priceHistory');

        if ($request->has('search')) {
            $query->search($request->search);
        }
        if ($request->has('category_id')) {
            $query->inCategory($request->category_id);
        }
        if ($request->has('manufacturer')) {
            $query->byManufacturer($request->manufacturer);
        }
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereHas('priceHistory', function ($q) use ($request) {
                $q->whereBetween('cena', [$request->min_price, $request->max_price]);
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'price') {
            $query->leftJoin('cenu_vesture', function ($join) {
                $join->on('preces.preces_id', '=', 'cenu_vesture.preces_id')
                    ->whereRaw('cenu_vesture.cenas_id = (SELECT MAX(cenas_id) FROM cenu_vesture WHERE preces_id = preces.preces_id)');
            })->orderBy('cenu_vesture.cena', $sortOrder)->select('preces.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $latestPrice = $product->priceHistory()->latest()->with('store')->first();
            if ($latestPrice) {
                $product->price = $latestPrice->cena;
                $product->store = $latestPrice->store;
            }
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'category',
            'specifications',
            'priceHistory' => function ($query) {
                $query->with('store')->latest()->limit(10);
            }
        ]);

        $latestPrice = $product->priceHistory->first();
        if ($latestPrice) {
            $product->price = $latestPrice->cena;
            $product->store = $latestPrice->store;
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'required|string|max:255',
            'apraksts' => 'nullable|string',
            'razotajs' => 'nullable|string|max:255',
            'modelis' => 'nullable|string|max:255',
            'attels_url' => 'nullable|url',
            'kategorijas_id' => 'required|exists:kategorijas,kategorijas_id',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'nosaukums' => 'sometimes|string|max:255',
            'apraksts' => 'nullable|string',
            'razotajs' => 'nullable|string|max:255',
            'modelis' => 'nullable|string|max:255',
            'attels_url' => 'nullable|url',
            'kategorijas_id' => 'sometimes|exists:kategorijas,kategorijas_id',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully'
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function priceHistory(Product $product): JsonResponse
    {
        $history = $product->priceHistory()
            ->with('store')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:2|max:4',
            'product_ids.*' => 'exists:preces,preces_id'
        ]);

        $products = Product::with(['category', 'specifications'])
            ->whereIn('preces_id', $validated['product_ids'])
            ->get();

        $products->transform(function ($product) {
            $latestPrice = $product->priceHistory()->latest()->with('store')->first();
            if ($latestPrice) {
                $product->price = $latestPrice->cena;
                $product->store = $latestPrice->store;
            }
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
