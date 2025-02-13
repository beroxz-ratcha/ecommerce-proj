<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

Route::get('change/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return Redirect::back();
});

Route::middleware(['guestOrVerified'])->group(function () {
    Route::get('/', function () {
        return redirect('/home');
    });

    Route::get('/home', [IndexController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/shop', [ProductController::class, 'index'])->name('shop');
    Route::get('/category/all', [ProductController::class, 'byAllCategories'])->name('byAllCategories');
    Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('byCategory');
    Route::get('/product/{product:slug}', [ProductController::class, 'view'])->name('product.view');

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/delivery', [CartController::class, 'viewDelivery'])->name('delivery');
        Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
        Route::post('/remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
        Route::post('/update-quantity/{product:slug}', [CartController::class, 'updateQuantity'])->name('update-quantity');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.update');
    Route::post('/profile/password-update', [ProfileController::class, 'passwordUpdate'])->name('profile_password.update');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    Route::post('/delivered', [CheckoutController::class, 'checkoutDelivery'])->name('cart.delivered');
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder'])->name('cart.checkout-order');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/{order}', [OrderController::class, 'view'])->name('order.view');
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

Route::post('/webhook/stripe', [CheckoutController::class, 'webhook']);

require __DIR__ . '/auth.php';
