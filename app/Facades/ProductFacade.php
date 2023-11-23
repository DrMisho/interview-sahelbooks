<?php

namespace App\Facades;

class ProductFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ProductService';
    }
}