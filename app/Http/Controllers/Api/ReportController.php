<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $totalTransactions = Purchase::count();
        $totalRevenue = Purchase::sum('total_price');

        $bestSellingProduct = PurchaseItem::query()
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_transactions' => $totalTransactions,
                'total_revenue' => (float) $totalRevenue,
                'best_selling_product' => $bestSellingProduct ? [
                    'product_id' => $bestSellingProduct->product_id,
                    'product_name' => $bestSellingProduct->product->name,
                    'total_qty_sold' => (int) $bestSellingProduct->total_qty,
                ] : null,
            ],
        ]);
    }

    public function purchases(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.date' => 'Format tanggal mulai tidak valid',
            'end_date.required' => 'Tanggal akhir wajib diisi',
            'end_date.date' => 'Format tanggal akhir tidak valid',
            'product_id.integer' => 'Product ID harus berupa angka',
            'product_id.exists' => 'Product ID tidak ditemukan',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $productId = $validated['product_id'] ?? null;

        $results = DB::select('CALL sp_report_purchases(?, ?, ?)', [
            $startDate,
            $endDate,
            $productId,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'filter' => [
                    'product_id' => $productId,
                ],
                'reports' => array_map(function ($row) {
                    return [
                        'tanggal' => $row->tanggal,
                        'nama_produk' => $row->nama_produk,
                        'total_transaksi' => (int) $row->total_transaksi,
                        'total_qty' => (int) $row->total_qty,
                        'total_amount' => (float) $row->total_amount,
                    ];
                }, $results),
                'summary' => [
                    'total_all_transactions' => collect($results)->sum('total_transaksi'),
                    'total_all_qty' => collect($results)->sum('total_qty'),
                    'total_all_amount' => collect($results)->sum('total_amount'),
                ],
            ],
        ]);
    }
}
