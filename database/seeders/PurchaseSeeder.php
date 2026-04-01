<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->get();

        if ($products->isEmpty()) {
            return;
        }

        // Ensure there is enough data to demonstrate pagination (per_page = 15)
        $targetPurchaseCount = 40;
        $currentCount = Purchase::count();

        if ($currentCount >= $targetPurchaseCount) {
            return;
        }

        $toCreate = $targetPurchaseCount - $currentCount;

        for ($i = 0; $i < $toCreate; $i++) {
            $date = now()->subDays($toCreate - $i)->toDateString();

            $selectedProducts = $products->shuffle()->take(rand(2, 4));
            $items = [];
            $totalPrice = 0;

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 5);
                $lineTotal = $product->price * $qty;
                $totalPrice += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $product->price,
                ];
            }

            $purchase = Purchase::create([
                'date' => $date,
                'total_price' => $totalPrice,
            ]);

            $purchase->purchaseItems()->createMany($items);
        }
    }
}
