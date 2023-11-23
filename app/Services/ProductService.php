<?php

namespace App\Services;

use App\Http\Constants\Constant;
use App\Models\Product;
use App\Services\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductService extends Service
{

    protected bool $condition_1 = false;
    protected bool $condition_2 = false;
    protected bool $condition_3 = false;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function queryResult(array $array = []): Builder
    {
        $query = $this->model::query();

        $query->when($array['id'] ?? false, fn($query, $id) => $query->where('id', $id)
        );        
        
        $query->when($array['ids'] ?? false, fn($query, $ids) => $query->whereIn('id', $ids)
        );

        $query->when($array['search'] ?? false, fn($query, $search) =>
            $query->where(function($query) use($search) {
                
                })
            );

        if($array['page'] ?? false)
        {
            $query->paginate($array['limit'] ?? config('app.pagination_limit'));
        }
        else
        {
            if(! $this->count)
            $query->paginate(config('app.pagination_limit'));
        }
        return $query;
    }

    public function getSubTotal(Collection $products): float
    {
        return $products->sum('price');
    }

    public function getShipping(Collection $products): float
    {
        $totalShipping = 0;
        foreach($products as $product)
        {
            $weight = $product->weight / 100.0;
            $shippingRate = $product->shippingRate->rate;

            $totalShipping += $shippingRate * $weight;
        }
        return $totalShipping;
    }

    public function getVAT(Collection $product): float
    {
        $subTotal = $this->getSubTotal($product);

        $VAT = $subTotal * 0.14;

        return $VAT;
    }

    public function hasDiscount(Collection $products): bool
    {
        $this->condition_1 = $this->hasShoes($products);
        
        $this->condition_2 = $this->hasMoreThanTwoTops($products);
        
        $this->condition_3 = $this->hasMoreThanTwoItems($products);

        return $this->condition_1 || $this->condition_2 || $this->condition_3;
    }

    public function getDetailedDiscount(Collection $products, &$shoesOffer, &$jacketOffer, &$shippingOffer)
    {
        if($this->condition_1)
            $shoesOffer = $products->where('category_id', Constant::CATEGORIES['SHOES'])->sum('price') * 0.1;

        if($this->condition_2)
            $jacketOffer = $products->where('category_id', Constant::CATEGORIES['JACKETS'])->first()->price * 0.5;

        if($this->condition_3)
            $shippingOffer = 10;

        return [
            $shoesOffer,
            $jacketOffer,
            $shippingOffer
        ];
    }

    protected function hasMoreThanTwoItems(Collection $products): bool
    {
        return $products->count() >= 2;
    }

    protected function hasMoreThanTwoTops(Collection $products): bool
    {
        return $products->where('category_id', Constant::CATEGORIES['TOPS'])->count() >= 2;
    }

    protected function hasShoes(Collection $products): bool
    {
        return $products->where('category_id', Constant::CATEGORIES['SHOES'])->count() >= 1;
    }
}