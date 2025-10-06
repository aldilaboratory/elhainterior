<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AllProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataAdminController;
use App\Http\Controllers\DataCustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderAdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\StocksReportController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Route admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route untuk Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', action: [DashboardController::class, 'index'])->name('dashboard');

    // penting: pastikan parameter resource pakai {order}
    Route::resource('orders', OrderAdminController::class)
         ->parameters(['orders' => 'order']);

    // route ekstra untuk ubah status (packing/shipped/completed/dll)
    Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])
         ->name('orders.update-status');
    Route::resource('products', ProductController::class)->parameters(['products' => 'id']);
    Route::resource('categories', CategoryController::class)->parameters(['categories' => 'id']);
    Route::resource('subcategories', SubcategoryController::class)->parameters(['subcategories' => 'id']);
    Route::resource('sales-report', SalesReportController::class);
    Route::get('/admin/sales-report/export-pdf', [SalesReportController::class, 'exportPdf'])
    ->name('admin.sales-report.export-pdf');
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

    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/make-default', [AddressController::class, 'makeDefault'])->name('addresses.make-default');

    // Autocomplete tujuan (kalau kamu pakai pencarian Komerce di modal alamat)
    Route::get('/ajax/destination/search', [AddressController::class, 'searchDest'])
        ->name('ajax.destination.search');

    // AJAX hitung ongkir
    Route::post('/ajax/shipping/cost', [ShippingController::class, 'cost'])
        ->name('ajax.shipping.cost');

    Route::post('/ajax/shipping/quote', [ShippingController::class, 'quote'])
    ->name('ajax.shipping.quote');

    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('customer.checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
    Route::get('/thank-you/{code}', function($code){
        $order = Order::where('order_code',$code)->with('items.product')->firstOrFail();
        return view('customer.checkout.thankyou', compact('order'));
    })->name('customer.thankyou');

    Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('customer.my-orders');
    Route::get('/my-orders/{order:order_code}', [CustomerOrderController::class, 'show'])
        ->name('customer.my-orders.show');
    Route::get('/my-orders/{order:order_code}/invoice', [CustomerOrderController::class, 'invoice'])
    ->name('customer.my-orders.invoice');

    Route::get('/pay/{order}', [CheckoutController::class, 'pay'])
         ->name('customer.pay');

    // Route::get('/my-orders/{order}', [CustomerOrderController::class, 'show'])
    // ->name('customer.orders.show')
    // ->whereNumber('order');
});

Route::get('/', [HomeController::class, 'index'])->name('customer.home');

Route::get('/ajax/destination/search', [AddressController::class, 'searchDest'])
    ->name('ajax.destination.search');

Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('customer.category');
Route::get('/kategori/{category:slug}/{subcategory:slug}', [CategoryController::class, 'showSub'])->name('customer.subcategory');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/item/{item}/update', [CartController::class, 'updateItem'])->name('cart.item.update');
Route::delete('/cart/item/{item}', [CartController::class, 'removeItem'])->name('cart.item.remove');

// About Us
Route::get('/about-us', function () {
    return view('customer.about-us');
})->name('customer.about-us');

// Contact Us
Route::get('/contact-us', function () {
    return view('customer.contact-us');
})->name('customer.contact-us');

// Contact Us
Route::get('/all-products', [AllProductsController::class, 'index'])
    ->name('customer.all-products');

// Cart
Route::get('/produk/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('customer.product-details');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
Route::post('/midtrans/webhook', [PaymentWebhookController::class, 'handle'])
    ->name('midtrans.webhook');
Route::get('/pay/{order}', [CheckoutController::class, 'pay'])->name('customer.pay.snap')->middleware('auth');
Route::get('/payment/finish',   [PaymentWebhookController::class,'finish'])->name('midtrans.finish');
Route::get('/payment/unfinish', [PaymentWebhookController::class,'unfinish'])->name('midtrans.unfinish');
Route::get('/payment/error',    [PaymentWebhookController::class,'error'])->name('midtrans.error');


require __DIR__.'/auth.php';