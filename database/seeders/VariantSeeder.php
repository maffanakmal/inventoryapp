<?php

namespace Database\Seeders;

use App\Models\Products;
use App\Models\Variants;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $productIds = Products::pluck('product_id')->toArray();

        if (empty($productIds)) {
            $this->command->warn('Tidak ada produk di database. Jalankan ProductSeeder dulu.');
            return;
        }

        foreach ($productIds as $productId) {
            
            $variantCount = rand(2, 6);

            for ($i = 0; $i < $variantCount; $i++) {
                Variants::create([
                    'product_id' => $productId,
                    'sku' => strtoupper('SKU-' . uniqid()),
                    'variant_name' => $faker->randomElement([
                        'Small', 'Medium', 'Large', 'Extra Large',
                        'Red', 'Blue', 'Green', 'Black', 'White',
                    ]),
                    'variant_image' => null,
                    'variant_price' => $faker->numberBetween(10000, 200000),
                    'stock_quantity' => $faker->numberBetween(10, 100),
                ]);
            }
        }
    }
}
