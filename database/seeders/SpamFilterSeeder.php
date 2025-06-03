<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SpamFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SpamFilter::factory()->count(10)->create();
    }
}
