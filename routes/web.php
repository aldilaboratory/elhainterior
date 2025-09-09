<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataAdminController;
use App\Http\Controllers\DataCustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StocksReportController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;





// Route admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route untuk Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('orders', OrderController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubcategoryController::class);
    Route::resource('sales-report', SalesReportController::class);
    Route::resource('stocks-report', StocksReportController::class);
    Route::resource('data-admin', DataAdminController::class);
    Route::resource('data-customer', DataCustomerController::class);
});

// Route Customer
Route::middleware(['auth', 'siswa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', function () {
    return view('customer.index');
})->name('customer.index');

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
