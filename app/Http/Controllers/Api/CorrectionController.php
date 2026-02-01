<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Correction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CorrectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Correction::with(['user', 'product']);

        if ($request->user()->isEditor()) {
            $query->pending();
        } else {
            $query->where('lietotaja_id', $request->user()->lietotaja_id);
        }

        $corrections = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $corrections
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preces_id' => 'required|exists:preces,preces_id',
            'labojuma_teksts' => 'required|string',
        ]);

        $correction = Correction::create([
            'lietotaja_id' => $request->user()->lietotaja_id,
            'preces_id' => $validated['preces_id'],
            'labojuma_teksts' => $validated['labojuma_teksts'],
            'statuss' => 'iesniegts',
        ]);

        return response()->json([
            'success' => true,
            'data' => $correction->load(['product']),
            'message' => 'Correction submitted successfully'
        ], 201);
    }

    public function show(Correction $correction): JsonResponse
    {
        $correction->load(['user', 'product', 'approvedBy']);

        return response()->json([
            'success' => true,
            'data' => $correction
        ]);
    }

    public function approve(Request $request, Correction $correction): JsonResponse
    {
        $correction->update([
            'statuss' => 'apstiprināts',
            'apstiprinaja_id' => $request->user()->lietotaja_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $correction,
            'message' => 'Correction approved'
        ]);
    }

    public function reject(Request $request, Correction $correction): JsonResponse
    {
        $correction->update([
            'statuss' => 'noraidīts',
            'apstiprinaja_id' => $request->user()->lietotaja_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $correction,
            'message' => 'Correction rejected'
        ]);
    }
}
