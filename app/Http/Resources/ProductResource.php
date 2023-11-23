<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item' => $this->item,
            'price' => $this->price,
            'category' => $this->category,
            'country_code' => $this->shippingRate->country_code,
            'shipping_rate' => $this->shippingRate,
            'weight' => $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
