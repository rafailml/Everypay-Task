# EveryPay Task

## INTRO

Everypay payment gateway is a service that integrates different payment methods under a common interface. One of our daily tasks is to integrate more payment methods by keeping the interface to implementors clean and agnostic.

## OBJECTIVES

Your task is to create a REST API web service, that integrates at least 2 different PSP (Payment Service Providers) and exposes a common interface. The API should have only one resource available; to charge a payment card. Every request to the API must be authenticated. You also have to create a Merchant account object where you should store the info about PSP credentials and Authentication credentials that are required to execute a charge.

Each Merchant account should have enabled only one PSP.

## DELIVERABLES

We expect you to deliver:
- A PHP 7.3 application that accepts a payment card dictionary [card_number, expiration_date, CVV, cardholder_name] and an amount. Then, according to the merchant settings will charge the payment card to the respective PSP.
- A brief document about your design and implementation.
- A PHPunit test suite of your code.

## NOTES

- Dependencies should be managed by composer.
- Your code should be following PSR-1 coding standards and PSR-2 coding style.
- You are freely to use any storage system you want, even plain arrays in PHP files as long as your code is data storage
  agnostic.
- It is recommended to use strict types for your PHP code.

## TIPS

These are some PSPs where you can register for free a sandbox account.
- Stripe [https://dashboard.stripe.com/register](https://dashboard.stripe.com/register)
- Paymill [https://app.paymill.com/user/register](https://app.paymill.com/user/register)
- Pin payments [https://pinpayments.com/get-started](https://pinpayments.com/get-started)


### TODO
- At least 2 Payment Service Providers with common interface
- The API should have only one resource available; to charge a payment card.
- Every request to the API must be authenticated.
- Merchant account object where you should store the info about PSP credentials and Authentication credentials that are required to execute a charge.
- Each Merchant account should have enabled only one PSP
- Application that accepts a payment card dictionary [card_number, expiration_date, CVV, cardholder_name] and an amount.
- Then, according to the merchant settings will charge the payment card to the respective PSP.
- A brief document about your design and implementation.
- A PHPunit test suite of your code.


## Design and implementation
1. PinPayments needs more parameters than Stripe, so I extended the list of params for payments
2. Paymill website does not work, so I have only two PSP
3. Some of PinPayments test card does not work as described in their site. For example 3D secure always succeeds while it should return "pending". I wrote a test for that, but it fails. When PinPayments fix that it should be OK.
4. The implementation is based on Strategy pattern. The main goal is to pay and payment methods (PSP) are strategies.
