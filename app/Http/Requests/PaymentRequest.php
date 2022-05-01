<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'credit_card_number' => 'required|numeric|digits:16',
            'expiration_date' => 'required|string|size:5|date_format:m/y|after:today',
            'cvv' => 'required|numeric|digits:3',
            'cardholder_name' => 'required|string',
            'merchant_id' => 'required|exists:merchants,id',
            'amount' => 'required|numeric',
            'currency' => 'required|string|exists:currencies,iso',
            'card_address' => 'string',
            'card_city' => 'string',
            'card_country' => 'string',
            'email' => 'string',
            'description' => 'string',
        ];
    }
}
