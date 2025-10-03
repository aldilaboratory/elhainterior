<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataAdminController;
use App\Http\Controllers\DataCustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StocksReportController;
use App\Http\Controllers\SubcategoryController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Route admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route untuk Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', action: [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('orders', OrderController::class);
    Route::resource('products', ProductController::class)->parameters(['products' => 'id']);
    Route::resource('categories', CategoryController::class)->parameters(['categories' => 'id']);
    Route::resource('subcategories', SubcategoryController::class)->parameters(['subcategories' => 'id']);
    Route::resource('sales-report', SalesReportController::class);
    Route::resource('stocks-report', StocksReportController::class);
    Route::resource('data-admin', DataAdminController::class)->parameters(['data-admin' => 'id']);
    Route::resource('data-customer', DataCustomerController::class);
});

// Route Customer
Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('welcome');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/checkout',  [CheckoutController::class, 'index'])->name('customer.checkout');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
        Route::get('/thank-you/{code}', function($code){
            $order = Order::where('order_code',$code)->with('items.product')->firstOrFail();
            return view('customer.checkout.thankyou', compact('order'));
        })->name('customer.thankyou');

    // Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])
    // ->name('customer.orders.show')
    // ->whereNumber('order');
});

Route::get('/', [HomeController::class, 'index'])->name('customer.home');

Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('customer.category');
Route::get('/kategori/{category:slug}/{subcategory:slug}', [CategoryController::class, 'showSub'])->name('customer.subcategory');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/item/{item}/update', [CartController::class, 'updateItem'])->name('cart.item.update');
Route::delete('/cart/item/{item}', [CartController::class, 'removeItem'])->name('cart.item.remove');
Route::post('/cart/item/{item}/update', [CartController::class,'updateItem'])
    ->name('cart.item.update');

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

// Cart
Route::get('/produk/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('customer.product-details');

require __DIR__.'/auth.php';