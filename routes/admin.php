<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:super-admin|admin|staff'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', Admin\ProductController::class);
    Route::get('products/{id}/variants', [Admin\ProductController::class, 'variants'])->name('products.variants');
    
    // Categories
    Route::resource('categories', Admin\CategoryController::class);
    
    // Brands
    Route::resource('brands', Admin\BrandController::class);
    
    // Orders
    Route::resource('orders', Admin\OrderController::class);
    Route::post('orders/{id}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{id}/tracking', [Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
    
    // Users
    Route::resource('users', Admin\UserController::class);
    
    // Coupons
    Route::resource('coupons', Admin\CouponController::class);
    
    // Banners
    Route::resource('banners', Admin\BannerController::class);
    
    // Settings
    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Reports
    Route::get('reports/sales', [Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [Admin\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/customers', [Admin\ReportController::class, 'customers'])->name('reports.customers');
    
    // Courier Management
    Route::get('couriers', [Admin\CourierController::class, 'index'])->name('couriers.index');
    Route::post('couriers/{code}/ship', [Admin\CourierController::class, 'createShipment'])->name('couriers.ship');
    Route::get('couriers/track/{trackingId}', [Admin\CourierController::class, 'trackShipment'])->name('couriers.track');
});