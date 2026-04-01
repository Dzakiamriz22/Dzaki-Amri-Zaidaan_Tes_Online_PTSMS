<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(): JsonResponse
    {
        $purchases = Purchase::query()
            ->with(['purchaseItems.product'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $totalPrice = collect($validated['items'])
                ->sum(fn (array $item) => $item['qty'] * $item['price']);

            $purchase = Purchase::create([
                'date' => $validated['date'],
                'total_price' => $totalPrice,
            ]);

            $purchase->purchaseItems()->createMany($validated['items']);

            return $purchase->load(['purchaseItems.product']);
        });

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ], 201);
    }
}
