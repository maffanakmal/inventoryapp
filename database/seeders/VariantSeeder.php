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

        $products = Products::all();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada produk di database. Jalankan ProductSeeder dulu.');
            return;
        }

        foreach ($products as $product) {

            $variantCount = rand(1, 6);

            for ($i = 0; $i < $variantCount; $i++) {
                $variantName = $faker->randomElement([
                    'Warna Merah', 'Warna Hitam', 'Warna Biru',
                    'Ukuran S', 'Ukuran M', 'Ukuran L', 'Ukuran XL',
                    'Model A', 'Model B', 'Model C'
                ]);

                Variants::create([
                    'product_id' => $product->product_id,
                    'sku' => strtoupper($faker->bothify('SKU-###??')),
                    'variant_name' => $variantName,
                    'variant_image' => $faker->imageUrl(400, 400, 'product', true, $variantName),
                    'variant_price' => $faker->randomFloat(2, 10000, 500000),
                    'stock_quantity' => $faker->numberBetween(5, 100),
                ]);
            }
        }
    }
}
