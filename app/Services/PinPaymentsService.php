<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Interfaces\PaymentStrategy;
use App\Models\Merchant;
use App\Traits\ConsumesExternalServices;

class PinPaymentsService implements PaymentStrategy
{
    use ConsumesExternalServices;

    protected $baseUri;

    protected $secret;

    public function __construct(int $merchantId)
    {
        $this->baseUri = config('services.pinpayments.base_uri');

        $merchant = Merchant::findOrFail($merchantId);
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
        $date = explode("/", $cardExpirationDate);

        $result = $this->makeRequest(
            'POST',
            "/1/charges",
            [],
            [
                'amount' => round($amount * CurrencyHelper::resolveFactor($currency)),
                'currency' => $currency,
                'card' => [
                    'number' => $cardNumber,
                    'expiry_month' => $date[0],
                    'expiry_year' => $date[1],
                    'cvc' => $cardCvc,
                    'name' => $cardholderName,
                    'address_line1' => $cardAddress,
                    'address_city' => $cardCity,
                    'address_country' => $cardCountry
                ],
                'email' => $email,
                'description' => $orderDescription
            ]
        );

        // Payment error
        if (isset($result->error)) {
            return response()->json(['message' => $result->error_description], 422);
        }

        // Other error
        if (isset($result->response->error_message)) {
            return response()->json(['message' => $result->response->error_message], 422);
        }

        // Success
        if ($result->response->status_message) {
            return response()->json([
                'message' => $result->response->status_message,
                'amount' => $result->response->amount / CurrencyHelper::resolveFactor($currency),
                'currency' => $result->response->currency,
                'description' => $result->response->description]);
        }

        return response()->json(['message' => 'Unable to handle payment'], 422);
    }

    protected function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $credentials = base64_encode($this->secret . ':');
        $headers['Authorization'] = "Basic " . $credentials;
    }

    protected function decodeResponse($response)
    {
        return json_decode($response);
    }

}
