<?php

namespace Tests\Unit\Services;

use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed --class CurrenciesSeeder');
        $this->artisan('db:seed --class MerchantSeeder');
    }

    /** @test */
    public function successful_payment()
    {
        $amount = 234.56;
        $currency = 'usd';
        $description = 'Test order';
        $service = new StripeService(1);
        $result = $service->handlePayment(
            '4242424242424242',
            '03/25',
            123,
            'John Doe',
            $amount,
            $currency,
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            $description
        );

        $this->assertEquals('Payment complete.', $result->getData()->message);
        $this->assertEquals($amount, $result->getData()->amount);
        $this->assertEquals(strtolower($currency), $result->getData()->currency);
    }
}
