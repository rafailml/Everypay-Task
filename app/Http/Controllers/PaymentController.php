<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function pay(PaymentRequest $request): JsonResponse
    {
        // 1. Resolve merchant payment method
        $paymentProviderName = strtolower(Merchant::find($request->validated()['merchant_id'])->payment_provider);
        if (!$paymentProviderName) {
            return response()->json(['message' => 'Payment provider not set for this merchant_id: ' . $request->validated()['merchant_id']], 500);
        }

        $serviceName = config("services.{$paymentProviderName}.class");
        if (!$serviceName) {
            return response()->json(['message' => 'Service not found: ' . $serviceName], 500);
        }

        try {
            $service = resolve($serviceName, ['merchantId' => $request->validated()['merchant_id']]);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Cannot resolve service: ' . $serviceName], 500);
        }

        // 2. Handle Payment
        return $service->handlePayment(
            (int)$request->validated()['credit_card_number'],
            $request->validated()['expiration_date'],
            (int)$request->validated()['cvv'],
            $request->validated()['cardholder_name'],
            (float)$request->validated()['amount'],
            $request->validated()['currency'],
            $request->validated()['card_address'],
            $request->validated()['card_city'],
            $request->validated()['card_country'],
            $request->validated()['email'],
            $request->validated()['description'],
        );
    }
}
