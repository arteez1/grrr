<?php

namespace App\Events;

use App\Models\VkCollection;
use Illuminate\Foundation\Events\Dispatchable;

class VkCollectionCreated
{
    use Dispatchable;

    public function __construct(public VkCollection $collection) {}

}
