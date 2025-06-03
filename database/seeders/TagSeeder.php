<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()->createMany([
            ['name' => 'Органика', 'type' => 'product'],
            ['name' => 'Новинка', 'type' => 'product'],
            ['name' => 'Акция', 'type' => 'post'],
            ['name' => 'Микрозелень', 'type' => 'vk', 'vk_tag_id' => 123],
        ]);
    }
}
