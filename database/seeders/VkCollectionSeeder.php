<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\VkCollection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VkCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VkCollection::factory(10)
            ->has(Product::factory()->count(5))
            ->create();
    }
}
