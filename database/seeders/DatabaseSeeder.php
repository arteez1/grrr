<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            ClientSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            RelatedProductSeeder::class,
            VkCollectionSeeder::class,
            DiscountSeeder::class,
            OrderSeeder::class,
            VkProductMetadataSeeder::class,
            // Other seeders...
        ]);

    }
}
