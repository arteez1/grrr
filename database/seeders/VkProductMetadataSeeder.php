<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\VkProductMetadata;
use Illuminate\Database\Seeder;

class VkProductMetadataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(50)
            ->has(VkProductMetadata::factory())
            ->create();
    }
}
