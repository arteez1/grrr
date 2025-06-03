<?php

namespace App\Events;

use App\Models\Product;

class VkSyncFailed
{
    public function __construct(
        public Product $product,
        public string $errorMessage
    ) {}
}
