<?php

namespace App\Services;
use App\Models\Product;

class RelatedProductService
{
    public function syncRelatedProducts(Product $product, array $relatedIds): void
    {
        $product->relatedProducts()->delete();

        foreach ($relatedIds as $relatedId) {
            if ($product->id != $relatedId) {
                $product->relatedProducts()->create([
                    'related_product_id' => $relatedId
                ]);
            }
        }
    }
}
