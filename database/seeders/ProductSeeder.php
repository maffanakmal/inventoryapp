<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products;
use App\Models\Category;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $categoryIds = Category::pluck('category_id')->toArray();

        if (empty($categoryIds)) {
            $this->command->warn('⚠️ Tidak ada kategori di database. Jalankan CategorySeeder dulu.');
            return;
        }

        for ($i = 1; $i <= 50; $i++) {
            Products::create([
                'product_name' => ucfirst($faker->words(2, true)),
                'category_id' => $faker->randomElement($categoryIds),
                'product_description' => $faker->sentence(8),
            ]);
        }
    }
}
