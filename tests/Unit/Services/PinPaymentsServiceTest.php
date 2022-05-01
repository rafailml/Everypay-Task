<?php

namespace Tests\Unit\Services;

use App\Services\PinPaymentsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinPaymentsServiceTest extends TestCase
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
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4200000000000000',
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

        $this->assertEquals('Success', $result->getData()->message);
        $this->assertEquals($amount, $result->getData()->amount);
        $this->assertEquals(strtoupper($currency), $result->getData()->currency);
        $this->assertEquals($description, $result->getData()->description);
    }

    /** @test */
    public function declined_card()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4100000000000001',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('The card was declined', $result->getData()->message);
    }

    /** @test */
    public function insufficient_funds()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4000000000000002',
            '03/25',
            123,
            'John Doe',
            '234.56',
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('There are not enough funds available to process the requested amount',
            $result->getData()->message);
    }

    /** @test */
    public function invalid_cvv()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4900000000000003',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('The card verification code (cvc) was incorrect',
            $result->getData()->message);
    }

    /** @test */
    public function invalid_card()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4800000000000004',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('The card was invalid',
            $result->getData()->message);
    }

    /** @test */
    public function processing_error()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4700000000000005',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('An error occurred while processing the card',
            $result->getData()->message);
    }

    /** @test */
    public function suspected_fraud()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4600000000000006',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('The transaction was flagged as possibly fraudulent and subsequently declined',
            $result->getData()->message);
    }

    /** @test */
    public function gateway_error()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4300000000000009',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('An upstream error occurred while processing the transaction. Please try again',
            $result->getData()->message);
    }

    /** @test */
    public function unknown_error()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4400000000000099',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('Sorry, an unknown error has occurred. This is being investigated',
            $result->getData()->message);
    }

    /** @test */
    public function secure_payment_3d()
    {
        $service = new PinPaymentsService(2);
        $result = $service->handlePayment(
            '4242424242424242',
            '03/25',
            123,
            'John Doe',
            234.56,
            'usd',
            'Buyer address',
            'Sofia',
            'Bulgaria',
            'test@example.com',
            'Test order'
        );

        $this->assertEquals('Pending',
            $result->getData()->message);
    }
}
