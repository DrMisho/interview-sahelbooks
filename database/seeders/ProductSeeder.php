<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'item' => 'T-shirt',
            'price' => 30.99,
            'category_id' => 1,
            'country_code' => 'US',
            'weight' => 200,
        ]);
        Product::create([
            'item' => 'Blouse',
            'price' => 10.99, 
            'category_id' => 1,
            'country_code' => 'UK',
            'weight' => 300,
        ]);
        Product::create([
            'item' => 'Pants',
            'price' => 64.99,
            'category_id' => 4,
            'country_code' => 'UK',
            'weight' => 900,
        ]);
        Product::create([
            'item' => 'Sweatpants',
            'price' => 84.99,
            'category_id' => 4,
            'country_code' => 'CN',
            'weight' => 1100,
        ]);
        Product::create([
            'item' => 'Jacket',
            'price' => 199.99,
            'category_id' => 2,
            'country_code' => 'US',
            'weight' => 2200,
        ]);
        Product::create([
            'item' => 'Shoes',
            'price' => 79.99,
            'category_id' => 3,
            'country_code' => 'CN',
            'weight' => 1300,
        ]);
    }
}
