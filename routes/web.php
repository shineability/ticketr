<?php

use App\Http\Controllers\CheckoutController;
use App\Payment\Mollie\Controllers\WebhookController as MollieWebhookController;
use App\Payment\Stripe\Controllers\WebhookController as StripeWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CheckoutController::class, 'index'])->name('home');

Route::prefix('checkout')->group(function () {
    Route::post('{ticket:uuid}', [CheckoutController::class, 'redirectToPaymentProvider'])->name('checkout');
    Route::get('{ticket:uuid}', [CheckoutController::class, 'showTicketForm'])->name('checkout.form');
    Route::get('order/{order:uuid}', [CheckoutController::class, 'redirectOrder'])->name('checkout.redirect.order');
});

Route::prefix('stripe')->group(function () {
    Route::post('webhook', [StripeWebhookController::class, '__invoke'])->name('stripe.webhook');
});

Route::prefix('mollie')->group(function () {
    Route::post('webhook', [MollieWebhookController::class, '__invoke'])->name('mollie.webhook');
});
