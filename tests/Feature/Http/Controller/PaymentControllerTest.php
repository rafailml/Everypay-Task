<?php

namespace Tests\Feature\Http\Controller;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed --class CurrenciesSeeder');
        $this->artisan('db:seed --class MerchantSeeder');
    }

    /** @test */
    public function user_can_pay_with_stripe_in_non_zero_decimal_currencies()
    {
        $user = User::factory()->create();
        $amount = 500.23;
        $currency = 'usd';

        $response = $this->actingAs($user)
            ->postJson('api/payments/pay', [
                'credit_card_number' => '4242424242424242',
                'expiration_date' => '04/25',
                'cvv' => 123,
                'cardholder_name' => 'John Doe',
                'merchant_id' => 1,
                'amount' => $amount,
                'currency' => $currency,
                'card_address' => 'Buyer address',
                'card_city' => 'Sofia',
                'card_country' => 'Bulgaria',
                'email' => 'test@example.com',
                'description' => 'Test order'
            ]);

        $response
            ->assertOk();

        $this->assertEquals('Payment complete.', $response->json('message'));
        $this->assertEquals($amount, $response->json('amount'));
        $this->assertEquals($currency, $response->json('currency'));
    }

    /** @test */
    public function user_can_pay_with_stripe_in_zero_decimal_currencies()
    {
        $user = User::factory()->create();
        $amount = 500.23;
        $currency = 'jpy';

        $response = $this->actingAs($user)
            ->postJson('api/payments/pay', [
                'credit_card_number' => '4242424242424242',
                'expiration_date' => '04/25',
                'cvv' => 123,
                'cardholder_name' => 'John Doe',
                'merchant_id' => 1,
                'amount' => $amount,
                'currency' => $currency,
                'card_address' => 'Buyer address',
                'card_city' => 'Sofia',
                'card_country' => 'Bulgaria',
                'email' => 'test@example.com',
                'description' => 'Test order'
            ]);

        $response
            ->assertOk();

        $this->assertEquals('Payment complete.', $response->json('message'));
        $this->assertEquals((int)$amount, $response->json('amount'));
        $this->assertEquals($currency, $response->json('currency'));
    }

    /** @test */
    public function user_can_pay_with_pinpayments_in_non_zero_decimal_currencies()
    {
        $user = User::factory()->create();
        $amount = 500.23;
        $currency = 'usd';
        $description = 'Test order';

        $response = $this->actingAs($user)
            ->postJson('api/payments/pay', [
                'credit_card_number' => '4200000000000000',
                'expiration_date' => '04/25',
                'cvv' => 123,
                'cardholder_name' => 'John Doe',
                'merchant_id' => 2,
                'amount' => $amount,
                'currency' => $currency,
                'card_address' => 'Buyer address',
                'card_city' => 'Sofia',
                'card_country' => 'Bulgaria',
                'email' => 'test@example.com',
                'description' => $description
            ]);

        $response
            ->assertOk();

        $this->assertEquals('Success', $response->json('message'));
        $this->assertEquals($amount, $response->json('amount'));
        $this->assertEquals(strtoupper($currency), $response->json('currency'));
        $this->assertEquals($description, $response->json('description'));
    }

    /** @test */
    public function user_can_pay_with_pinpayments_in_zero_decimal_currencies()
    {
        $user = User::factory()->create();
        $amount = 500.23;
        $currency = 'jpy';
        $description = 'Test order';

        $response = $this->actingAs($user)
            ->postJson('api/payments/pay', [
                'credit_card_number' => '4200000000000000',
                'expiration_date' => '04/25',
                'cvv' => 123,
                'cardholder_name' => 'John Doe',
                'merchant_id' => 2,
                'amount' => $amount,
                'currency' => $currency,
                'card_address' => 'Buyer address',
                'card_city' => 'Sofia',
                'card_country' => 'Bulgaria',
                'email' => 'test@example.com',
                'description' => $description
            ]);

        $response
            ->assertOk();

        $this->assertEquals('Success', $response->json('message'));
        $this->assertEquals((int)$amount, $response->json('amount'));
        $this->assertEquals(strtoupper($currency), $response->json('currency'));
        $this->assertEquals($description, $response->json('description'));
    }

    /** @test */
    public function unauthorized_users_cant_pay()
    {

        $response = $this
            ->postJson('api/payments/pay', [
                'credit_card_number' => '4200000000000000',
                'expiration_date' => '04/25',
                'cvv' => 123,
                'cardholder_name' => 'John Doe',
                'merchant_id' => 2,
                'amount' => 500.23,
                'currency' => 'jpy',
                'card_address' => 'Buyer address',
                'card_city' => 'Sofia',
                'card_country' => 'Bulgaria',
                'email' => 'test@example.com',
                'description' => 'Test order'
            ]);

        $response
            ->assertUnauthorized();
    }
}
