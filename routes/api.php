<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/payments/pay', [PaymentController::class, 'pay'])->name('pay');
});
