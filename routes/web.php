<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ShopController;
use App\Mail\ShopCreated;
use App\Models\Shop;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::inertia('/', 'Index')->name('home');

Route::post('create_shop', [ShopController::class, 'store']);
Route::get('queue/{uuid}', [ShopController::class, 'frontend'])->name('queue');
Route::get('shop/{uuid}', [ShopController::class, 'backend'])->name('shop');
Route::post('passcode', [ShopController::class, 'login']);
Route::post('call_next', [ShopController::class, 'next']);
Route::delete('reset/{uuid}', [ShopController::class, 'reset']);

Route::get('c/{link}', [ClientController::class, 'index'])->name('client');
Route::post('get_ticket', [ClientController::class, 'store']);
Route::delete('cancel_ticket/{uuid}', [ClientController::class, 'cancel']);
Route::get('ticket/{uuid}', [ClientController::class, 'show'])->name('ticket');

Route::get('mail', function () {
    $shop = Shop::first();
    $passcode = strtolower(Str::random(6));
    return new ShopCreated($shop, $passcode);
});

Route::get('stripe', function () {
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => 'price_1JSLMICfUP56S8Kwv0S1Nwoy',
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'client_reference_id' => 'q.jamwong.me',
        'success_url' => route('stripe_success'),
        'cancel_url' => route('home'),
    ]);

    return redirect($session->url);
})->name('coffee');

Route::inertia('stripe/success', 'Success')->name('stripe_success');
