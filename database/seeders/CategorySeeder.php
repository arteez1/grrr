<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\VkCategoryMapping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vkMapping = VkCategoryMapping::factory()->count(1);
        Category::factory(20)
            ->has($vkMapping, 'vkMappings')
            ->create();
    }
}
