<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
            ->paginate(15);

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
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ], [
            'date.required' => 'Tanggal pembelian wajib diisi',
            'date.date' => 'Format tanggal tidak valid',
            'items.required' => 'Items pembelian wajib diisi',
            'items.array' => 'Items pembelian harus berupa array',
            'items.min' => 'Minimum 1 item harus ada',
            'items.*.product_id.required' => 'Product ID wajib diisi di setiap item',
            'items.*.product_id.integer' => 'Product ID harus berupa angka',
            'items.*.product_id.exists' => 'Product ID tidak ditemukan di database',
            'items.*.qty.required' => 'Qty wajib diisi di setiap item',
            'items.*.qty.integer' => 'Qty harus berupa angka bulat',
            'items.*.qty.min' => 'Qty minimal 1',
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $totalPrice = 0;
            $purchaseItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemTotal = $product->price * $item['qty'];
                $totalPrice += $itemTotal;

                $purchaseItems[] = [
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $product->price,
                ];
            }

            $purchase = Purchase::create([
                'date' => $validated['date'],
                'total_price' => $totalPrice,
            ]);

            $purchase->purchaseItems()->createMany($purchaseItems);

            return $purchase->load(['purchaseItems.product']);
        });

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ], 201);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        $purchase->load(['purchaseItems.product']);

        return response()->json([
            'success' => true,
            'data' => $purchase,
        ]);
    }
}
