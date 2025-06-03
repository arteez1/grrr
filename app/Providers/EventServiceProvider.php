<?php

namespace App\Providers;

use App\Events\VkCollectionCreated;
use App\Events\VkCollectionUpdated;
use App\Listeners\SyncVkCollection;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VkCollectionCreated::class => [
            SyncVkCollection::class,
        ],
        VkCollectionUpdated::class => [
            SyncVkCollection::class,
        ],
    ];
}
