<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingRate::create([
            'country_code' => 'US',
            'rate' => 2,
        ]);
        ShippingRate::create([
            'country_code' => 'UK',
            'rate' => 3,
        ]);
        ShippingRate::create([
            'country_code' => 'CN',
            'rate' => 2,
        ]);
    }
}
