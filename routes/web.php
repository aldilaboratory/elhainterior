<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('customer.index');
})->name('customer.index');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route Customer
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// About Us
Route::get('/about-us', function () {
    return view('customer.about-us');
})->name('customer.about-us');

// Contact Us
Route::get('/contact-us', function () {
    return view('customer.contact-us');
})->name('customer.contact-us');

// Contact Us
Route::get('/shop-grid', function () {
    return view('customer.shop-grid');
})->name('customer.shop-grid');

// Checkout
Route::get('/checkout', function () {
    return view('customer.checkout');
})->name('customer.checkout');

// Cart
Route::get('/cart', function () {
    return view('customer.cart');
})->name('customer.cart');

// Cart
Route::get('/product-details', function () {
    return view('customer.product-details');
})->name('customer.product-details');

require __DIR__.'/auth.php';
