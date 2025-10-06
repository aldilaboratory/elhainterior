<?php

use App\Http\Controllers\PaymentWebhookController;

Route::post('/midtrans/webhook', [PaymentWebhookController::class, 'handle'])
    ->name('midtrans.webhook.api');