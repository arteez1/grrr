<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\RelatedProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelatedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(50)
            ->has(RelatedProduct::factory()->count(3), 'relatedProducts')
            ->create();
    }
}
