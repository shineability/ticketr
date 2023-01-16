<?php

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

Route::get('/', 'CheckoutController@index')->name('home');

Route::prefix('checkout')->group(function () {
    Route::post('{ticket:uuid}', 'CheckoutController@redirectToPaymentProvider')->name('checkout');
    Route::get('{ticket:uuid}', 'CheckoutController@showTicketForm')->name('checkout.form');
    Route::get('order/{order:uuid}', 'CheckoutController@redirectOrder')->name('checkout.redirect.order');
});

Route::prefix('stripe')->group(function () {
    Route::post('webhook', [\App\Payment\Stripe\Controllers\WebhookController::class, '__invoke'])->name('stripe.webhook');
});

Route::prefix('mollie')->group(function () {
    Route::post('webhook', [\App\Payment\Mollie\Controllers\WebhookController::class, '__invoke'])->name('mollie.webhook');
});
