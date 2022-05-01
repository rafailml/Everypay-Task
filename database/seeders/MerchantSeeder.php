<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Merchant::create([
            'name' => "Stripe Merchant",
            'payment_provider' => 'Stripe',
            'payment_provider_key' => 'pk_test_51KshoqFj60ELyC91Vy784QtYPx8MFczMY8fRYDiZD7DlEUWHvaCzXDDpTmqb0gbkPRgj9MFudKdieQGGHwR1MzvF00Jah7to0u',
            'payment_provider_secret' => 'sk_test_51KshoqFj60ELyC91Y30UGSBkfZsnkXOTyU5RTZeq3xIJnCT8ZpwY1RVQcOrzYt5YAmgcSMs9Vzg0J552TG4ZZmBK00yHyiK22v'
        ]);

        Merchant::create([
            'name' => "PinPayments Merchant",
            'payment_provider' => 'PinPayments',
            'payment_provider_key' => '',
            'payment_provider_secret' => 'ElysxZKM2vjOde5C58Rs9g'
        ]);
    }
}
