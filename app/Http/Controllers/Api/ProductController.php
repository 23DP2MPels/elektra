<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\Specification;
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
            'veikala_id' => 'nullable|exists:veikali,veikala_id',
            'cena' => 'nullable|numeric|min:0',
            'specs' => 'nullable|array',
            'specs.*.parametrs' => 'required_with:specs|string|max:255',
            'specs.*.vertiba' => 'required_with:specs|string|max:1000',
        ]);

        $productData = collect($validated)->only([
            'nosaukums',
            'apraksts',
            'razotajs',
            'modelis',
            'attels_url',
            'kategorijas_id',
        ])->toArray();

        $product = Product::create($productData);

        if (isset($validated['cena']) && isset($validated['veikala_id'])) {
            PriceHistory::create([
                'preces_id' => $product->preces_id,
                'veikala_id' => $validated['veikala_id'],
                'cena' => $validated['cena'],
                'iepriekšējā_cena' => null,
                'pieejams' => true,
            ]);
        }

        if (!empty($validated['specs']) && is_array($validated['specs'])) {
            foreach ($validated['specs'] as $spec) {
                if (
                    isset($spec['parametrs'], $spec['vertiba']) &&
                    $spec['parametrs'] !== '' &&
                    $spec['vertiba'] !== ''
                ) {
                    Specification::create([
                        'preces_id' => $product->preces_id,
                        'parametrs' => $spec['parametrs'],
                        'vertiba' => $spec['vertiba'],
                    ]);
                }
            }
        }

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
            'veikala_id' => 'sometimes|exists:veikali,veikala_id',
            'cena' => 'sometimes|numeric|min:0',
            'specs' => 'sometimes|array',
            'specs.*.parametrs' => 'required_with:specs|string|max:255',
            'specs.*.vertiba' => 'required_with:specs|string|max:1000',
        ]);

        $productData = collect($validated)->only([
            'nosaukums',
            'apraksts',
            'razotajs',
            'modelis',
            'attels_url',
            'kategorijas_id',
        ])->toArray();

        if (!empty($productData)) {
            $product->update($productData);
        }

        if (array_key_exists('cena', $validated)) {
            $latest = $product->priceHistory()->latest()->first();
            $storeId = $validated['veikala_id'] ?? ($latest?->veikala_id);

            if ($storeId) {
                PriceHistory::create([
                    'preces_id' => $product->preces_id,
                    'veikala_id' => $storeId,
                    'cena' => $validated['cena'],
                    'iepriekšējā_cena' => $latest?->cena,
                    'pieejams' => true,
                ]);
            }
        }

        if ($request->has('specs')) {
            $product->specifications()->delete();

            if (is_array($request->specs)) {
                foreach ($request->specs as $spec) {
                    if (
                        isset($spec['parametrs'], $spec['vertiba']) &&
                        $spec['parametrs'] !== '' &&
                        $spec['vertiba'] !== ''
                    ) {
                        Specification::create([
                            'preces_id' => $product->preces_id,
                            'parametrs' => $spec['parametrs'],
                            'vertiba' => $spec['vertiba'],
                        ]);
                    }
                }
            }
        }

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
