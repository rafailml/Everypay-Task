<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Interfaces\PaymentStrategy;
use App\Models\Merchant;
use App\Traits\ConsumesExternalServices;

class StripeService implements PaymentStrategy
{
    use ConsumesExternalServices;

    protected $baseUri;

    protected $key;

    protected $secret;

    public function __construct(int $merchantId)
    {
        $this->baseUri = config('services.stripe.base_uri');

        $merchant = Merchant::findOrFail($merchantId);
        $this->key = $merchant->payment_provider_key;
        $this->secret = $merchant->payment_provider_secret;
    }

    public function handlePayment(
        int    $cardNumber,
        string $cardExpirationDate,
        int    $cardCvc,
        string $cardholderName,
        float  $amount,
        string $currency,
        string $cardAddress = null,
        string $cardCity = null,
        string $cardCountry = null,
        string $email = null,
        string $orderDescription = null,
    )
    {
        // 1. Create PaymentMethod
        $paymentMethod = $this->createPaymentMethod($cardNumber, $cardExpirationDate, $cardCvc, $cardholderName);

        // 2. Create Intent
        $intent = $this->createIntent($amount, strtoupper($currency), $paymentMethod->id);

        // 3. Confirm Payment
        $confirmation = $this->confirmPayment($intent->id);

        if (isset($confirmation->error)) {
            // Payment error
            return response()->json(['message' => $confirmation->error->message], 422);
        }

        switch ($confirmation->status) {
            case 'requires_action':
                return response()->json(['message' => 'Open 3D confirmation', 'clientSecret' => $confirmation->client_secret]);
            case 'succeeded':
                return response()->json([
                    'message' => $confirmation->charges->data[0]->outcome->seller_message,
                    'amount' => $confirmation->amount / CurrencyHelper::resolveFactor($currency),
                    'currency' => $confirmation->currency
                ]);
            default:
                // Unknown status
                return response()->json(['message' => 'Unknown status', 'status' => $confirmation->status], 422);
        }
    }

    protected function createPaymentMethod(int $cardNumber, string $expirationDate, int $cvc, string $cardholderName,)
    {
        $date = explode("/", $expirationDate);
        return $this->makeRequest(
            'POST',
            '/v1/payment_methods',
            [],
            [
                'type' => 'card',
                'card' => [
                    'number' => $cardNumber,
                    'exp_month' => $date[0],
                    'exp_year' => $date[1],
                    'cvc' => $cvc
                ],
                'billing_details' => [
                    'name' => $cardholderName
                ]
            ],
        );
    }

    protected function createIntent($value, $currency, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * CurrencyHelper::resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethod,
                'confirmation_method' => 'manual',
            ],
        );
    }

    protected function confirmPayment($paymentIntentId)
    {
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm",
        );
    }

    protected function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = "Bearer {$this->secret}";
    }

    protected function decodeResponse($response)
    {
        return json_decode($response);
    }
}
