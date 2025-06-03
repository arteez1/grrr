<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\VkCollection;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Тестовые товары
        Product::factory(50)
            ->has(Category::factory()->count(2))
            ->has(VkCollection::factory()->count(1))
            ->create();
    }
}
