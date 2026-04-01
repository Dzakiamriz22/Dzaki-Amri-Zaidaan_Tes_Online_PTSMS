<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01'],
        ], [
            'name.required' => 'Nama produk wajib diisi',
            'name.string' => 'Nama produk harus berupa teks',
            'name.max' => 'Nama produk maksimal 255 karakter',
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga produk harus berupa angka',
            'price.min' => 'Harga produk minimal 0.01',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0.01'],
        ], [
            'name.string' => 'Nama produk harus berupa teks',
            'name.max' => 'Nama produk maksimal 255 karakter',
            'price.numeric' => 'Harga produk harus berupa angka',
            'price.min' => 'Harga produk minimal 0.01',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'data' => null,
        ]);
    }
}
