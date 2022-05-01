<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            'usd' => 'US Dollar',
            'eur' => 'Euro',
            'gbp' => 'English Pound',
            'jpy' => 'Japanese Yen'
        ];

        foreach ($currencies as $iso => $name) {
            Currency::create([
                'iso' => $iso,
                'name' => $name
            ]);
        }
    }
}
