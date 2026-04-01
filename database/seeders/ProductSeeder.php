<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Pensil 2B', 'price' => 5000],
            ['name' => 'Pensil HB', 'price' => 4500],
            ['name' => 'Penghapus', 'price' => 3000],
            ['name' => 'Penggaris 30cm', 'price' => 8000],
            ['name' => 'Busur Derajat', 'price' => 12000],
            ['name' => 'Jangka Sorong', 'price' => 15000],
            ['name' => 'Pulpen Biru', 'price' => 6000],
            ['name' => 'Pulpen Merah', 'price' => 6000],
            ['name' => 'Buku Tulis A4', 'price' => 25000],
            ['name' => 'Buku Gambar A3', 'price' => 30000],
            ['name' => 'Kertas HVS A4', 'price' => 45000],
            ['name' => 'Spidol Hitam', 'price' => 9000],
            ['name' => 'Spidol Biru', 'price' => 9000],
            ['name' => 'Map Plastik', 'price' => 7000],
            ['name' => 'Binder A5', 'price' => 18000],
            ['name' => 'Sticky Notes', 'price' => 10000],
            ['name' => 'Correction Tape', 'price' => 11000],
            ['name' => 'Lem Kertas', 'price' => 8000],
            ['name' => 'Gunting', 'price' => 14000],
            ['name' => 'Stapler Mini', 'price' => 22000],
            ['name' => 'Isi Stapler', 'price' => 6000],
            ['name' => 'Klip Kertas', 'price' => 5000],
            ['name' => 'Amplop Coklat', 'price' => 4000],
            ['name' => 'Tinta Printer', 'price' => 85000],
            ['name' => 'Label Stiker', 'price' => 13000],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                ['price' => $product['price']]
            );
        }
    }
}

