<?php

use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;


Route::post('/midtrans/notification', [POSController::class, 'midtransNotification'])
    ->name('midtrans.notification');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
    return redirect()->route('login');
});

    Route::get('/dashboard', [POSController::class, 'index'])->name('dashboard');
    Route::post('/cart/add', [POSController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/clear', [POSController::class, 'clearCart'])
    ->name('cart.clear');
    Route::get('/remove/{id}', [POSController::class, 'removeFromCart']);
    Route::post('/cart/update-quantity/{id}', [POSController::class, 'updateCartQuantity'])->name('cart.updateQuantity');
    Route::post('/products/{id}/toggle-favorite', [POSController::class, 'toggleFavorite'])->name('products.toggleFavorite');
     Route::post('/checkout', [POSController::class, 'checkout']);
    Route::get('/products', [POSController::class, 'products'])
    ->name('products');
    Route::get('/transactions', [POSController::class, 'transactions'])
    ->name('transactions');
    Route::get('/transactions/summary', [POSController::class, 'transactionSummary'])
    ->name('transactions.summary');
    Route::post('/orders/{orderId}/check-status', [POSController::class, 'checkOrderStatus'])
    ->name('orders.checkStatus');
    Route::get('/transactions/export', [POSController::class, 'exportTransactions'])
    ->name('transactions.export');
    Route::get('/profile', [ProfileController::class, 'index'])
    ->name('profile.page');

Route::post('/profile/update', [ProfileController::class, 'updateProfile'])
    ->name('profile.update');

Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
    ->name('profile.password');

Route::post('/profile/toggle-dark-mode', [ProfileController::class, 'toggleDarkMode'])
    ->name('profile.darkmode');
});

Route::middleware(['auth','admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
    Route::get('/admin/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    Route::post('/admin/products/store', [ProductController::class, 'store'])
        ->name('products.store');
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])
    ->name('products.edit');
    Route::post('/admin/products/{id}/update', [ProductController::class, 'update'])
    ->name('products.update');
Route::delete('/admin/products/{id}/force-delete', [ProductController::class, 'forceDelete'])
    ->name('products.forceDelete');
    Route::post('/admin/products/{id}/update-stock', [ProductController::class, 'updateStock'])
    ->name('products.updateStock');

    Route::delete('/admin/products/{id}/delete', [ProductController::class, 'destroy'])
    ->name('products.delete');

    Route::post('/admin/products/{id}/activate', [ProductController::class, 'activate'])
    ->name('products.activate');

    Route::post('/admin/toggle-report', [AdminController::class, 'toggleReport'])
    ->name('admin.toggle.report');

    Route::post('/admin/send-report-now', [AdminController::class, 'sendReportNow'])
    ->name('admin.send.report.now');
    

    Route::get('/admin/analytics', [AdminController::class, 'analytics'])
    ->name('admin.analytics');

     Route::get('/admin/accounts',
            [AdminController::class, 'accounts'])
            ->name('admin.accounts');

    Route::post('/admin/accounts/store',
            [AdminController::class, 'storeAccount'])
            ->name('admin.accounts.store');
    Route::put('/admin/accounts/{user}', [AdminController::class, 'updateAccount'])
    ->name('admin.accounts.update');

Route::delete('/admin/accounts/{user}', [AdminController::class, 'deleteAccount'])
    ->name('admin.accounts.destroy');

});
require __DIR__.'/auth.php';
