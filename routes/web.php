<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage (Storefront)
Route::get('/', [Controllers\HomeController::class, 'index'])->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/returns', [PageController::class, 'returns'])->name('returns');

// Public variant lookup (for frontend)
Route::get('products/{product}/get-variant', [App\Http\Controllers\Admin\ProductVariantController::class, 'getVariant'])->name('products.get-variant');


Route::get('/contact', [Controllers\HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [Controllers\HomeController::class, 'contactSubmit'])->name('contact.submit');
// Newsletter
Route::post('/newsletter/subscribe', [Controllers\HomeController::class, 'newsletterSubscribe'])->name('newsletter.subscribe');
// Language & Currency
// Route::get('/language/{locale}', [Controllers\HomeController::class, 'changeLanguage'])->name('language.change');
// Route::get('/currency/{currency}', [Controllers\HomeController::class, 'changeCurrency'])->name('currency.change');



// Products
Route::prefix('products')->name('product.')->group(function () {
    Route::get('/', [Controllers\ProductController::class, 'index'])->name('index');
    Route::get('/search', [Controllers\ProductController::class, 'search'])->name('search');    
    Route::get('/suggestions', [Controllers\ProductController::class, 'suggestions'])->name('suggestions');
    Route::get('/featured', [Controllers\ProductController::class, 'featured'])->name('featured');
    Route::get('/new-arrivals', [Controllers\ProductController::class, 'newArrivals'])->name('new');
    Route::get('/sale', [Controllers\ProductController::class, 'sale'])->name('sale');
    Route::get('/category/{slug}', [Controllers\ProductController::class, 'byCategory'])->name('category');
    Route::get('/brand/{slug}', [Controllers\ProductController::class, 'byBrand'])->name('brand');
    Route::get('/quick-view/{id}', [Controllers\ProductController::class, 'quickView'])->name('quick-view');
    Route::get('/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('show');
});

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [Controllers\CartController::class, 'index'])->name('index');
    Route::post('/add', [Controllers\CartController::class, 'add'])->name('add');
    Route::post('/update/{itemId}', [Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemId}', [Controllers\CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [Controllers\CartController::class, 'clear'])->name('clear');
    Route::post('/apply-coupon', [Controllers\CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/remove-coupon', [Controllers\CartController::class, 'removeCoupon'])->name('remove-coupon');
    Route::get('/summary', [Controllers\CartController::class, 'getSummary'])->name('summary');
});

// Checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [Controllers\CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [Controllers\CheckoutController::class, 'success'])->name('success');
    Route::get('/cancel/{order?}', [Controllers\CheckoutController::class, 'cancel'])->name('cancel');
});

Route::get('order/{order}/invoice/download', [App\Http\Controllers\OrderInvoiceController::class, 'download'])
    ->name('order.invoice.download'); // Signed URL for security

Route::get('order/{order}/invoice/print', [App\Http\Controllers\OrderInvoiceController::class, 'print'])
    ->name('order.invoice.print');
    

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [App\Http\Controllers\WishlistController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\WishlistController::class, 'add'])->name('add');
    Route::post('/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('toggle');
    Route::delete('/remove/{product}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('remove');
    Route::post('/clear', [App\Http\Controllers\WishlistController::class, 'clear'])->name('clear');
    Route::get('/check/{product}', [App\Http\Controllers\WishlistController::class, 'check'])->name('check');
    Route::get('/count', [App\Http\Controllers\WishlistController::class, 'count'])->name('count');
    Route::post('/move-to-cart/{product}', [App\Http\Controllers\WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// Compare
Route::prefix('compare')->name('compare.')->group(function () {
    Route::get('/', [Controllers\CompareController::class, 'index'])->name('index');
    Route::post('/add', [Controllers\CompareController::class, 'add'])->name('add');
    Route::delete('/remove/{productId}', [Controllers\CompareController::class, 'remove'])->name('remove');
    Route::post('/clear', [Controllers\CompareController::class, 'clear'])->name('clear');
    Route::get('/check/{productId}', [Controllers\CompareController::class, 'check'])->name('check');
    Route::get('/table', [Controllers\CompareController::class, 'getTable'])->name('table');
});

// Payment
Route::prefix('payment')->name('payment.')->group(function () {
    // SSLCommerz
    Route::post('/sslcommerz/success', [Controllers\PaymentController::class, 'sslCommerzSuccess'])->name('sslcommerz.success');
    Route::post('/sslcommerz/fail', [Controllers\PaymentController::class, 'sslCommerzFail'])->name('sslcommerz.fail');
    Route::post('/sslcommerz/cancel', [Controllers\PaymentController::class, 'sslCommerzCancel'])->name('sslcommerz.cancel');
    Route::post('/sslcommerz/ipn', [Controllers\PaymentController::class, 'sslCommerzIpn'])->name('sslcommerz.ipn');
    
    // Stripe
    Route::post('/stripe/webhook', [Controllers\PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');
    
    // bKash
    Route::get('/bkash/callback', [Controllers\PaymentController::class, 'bkashCallback'])->name('bkash.callback');
    
    // Nagad
    Route::get('/nagad/callback', [Controllers\PaymentController::class, 'nagadCallback'])->name('nagad.callback');
    
    // Common
    Route::get('/status/{order}', [Controllers\PaymentController::class, 'getPaymentStatus'])->name('status');
    Route::get('/retry/{order}', [Controllers\PaymentController::class, 'retryPayment'])->name('retry');
});

// Courier Webhooks
Route::prefix('courier')->name('courier.')->group(function () {
    Route::post('/pathao/webhook', [Controllers\CourierWebhookController::class, 'pathao'])->name('pathao.webhook');
    Route::post('/steadfast/webhook', [Controllers\CourierWebhookController::class, 'steadfast'])->name('steadfast.webhook');
    Route::post('/redx/webhook', [Controllers\CourierWebhookController::class, 'redx'])->name('redx.webhook');
});

// Review Routes
Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
Route::get('/reviews/product/{productId}', [App\Http\Controllers\ReviewController::class, 'getProductReviews'])->name('reviews.product');
Route::get('/reviews/stats/{productId}', [App\Http\Controllers\ReviewController::class, 'getReviewStats'])->name('reviews.stats');

// Sitemap & SEO
Route::get('/sitemap.xml', [Controllers\SitemapController::class, 'index'])->name('sitemap.main');
Route::get('/sitemap-images.xml', [Controllers\SitemapController::class, 'images'])->name('sitemap.images');
Route::get('/sitemap-index.xml', [Controllers\SitemapController::class, 'indexSitemap'])->name('sitemap.index');
Route::get('/robots.txt', function () {
    return response()->view('seo.robots')->header('Content-Type', 'text/plain');
})->name('robots');

// Profile (Authenticated Users)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/dashboard', [Controllers\ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [Controllers\ProfileController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [Controllers\ProfileController::class, 'showOrder'])->name('order.show');
    // Route::get('/orders/{order}', [Controllers\ProfileController::class, 'orderDetails'])->name('order.details'); 
    Route::get('/wishlist', [Controllers\ProfileController::class, 'wishlist'])->name('wishlist');
    Route::get('/addresses', [Controllers\ProfileController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [Controllers\ProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [Controllers\ProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [Controllers\ProfileController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{address}/default', [Controllers\ProfileController::class, 'setDefaultAddress'])->name('addresses.default');
    Route::get('/settings', [Controllers\ProfileController::class, 'settings'])->name('settings');
    Route::put('/settings', [Controllers\ProfileController::class, 'updateSettings'])->name('update');
    Route::post('/avatar', [Controllers\ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::put('/password', [Controllers\ProfileController::class, 'updatePassword'])->name('password');
    Route::get('/', [Controllers\ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [Controllers\ProfileController::class, 'update'])->name('update');
    Route::get('/addresses/{address}/edit', [Controllers\ProfileController::class, 'editAddress'])->name('addresses.edit');
    Route::delete('/avatar', [Controllers\ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
    Route::delete('/', [Controllers\ProfileController::class, 'destroy'])->name('delete');
});

// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:super-admin,admin,staff'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::delete('products/images/{imageId}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.images.delete'); 
    Route::post('products/bulk-delete', [App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::post('products/bulk-status', [App\Http\Controllers\Admin\ProductController::class, 'bulkStatus'])->name('products.bulk-status');
    Route::get('products/export/csv', [App\Http\Controllers\Admin\ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::post('products/import/csv', [App\Http\Controllers\Admin\ProductController::class, 'importCsv'])->name('products.import.csv');
    
    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::post('categories/{category}/move-up', [App\Http\Controllers\Admin\CategoryController::class, 'moveUp'])->name('categories.move-up');
    Route::post('categories/{category}/move-down', [App\Http\Controllers\Admin\CategoryController::class, 'moveDown'])->name('categories.move-down');
    Route::post('categories/bulk-delete', [App\Http\Controllers\Admin\CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    
    // Brands
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);
    Route::post('brands/bulk-delete', [App\Http\Controllers\Admin\BrandController::class, 'bulkDelete'])->name('brands.bulk-delete');
    Route::post('brands/bulk-featured', [App\Http\Controllers\Admin\BrandController::class, 'bulkFeatured'])->name('brands.bulk-featured');
    
    // Orders
    Route::get('orders/report', [App\Http\Controllers\Admin\OrderController::class, 'report'])->name('orders.report');
    Route::get('orders/print-report', [App\Http\Controllers\Admin\OrderController::class, 'printReport'])->name('orders.print-report');
    Route::get('orders/export/csv', [App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export.csv');
    Route::get('orders/invoice/{order}', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/print/{order}', [App\Http\Controllers\Admin\OrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/tracking', [App\Http\Controllers\Admin\OrderController::class, 'addTracking'])->name('orders.tracking');
    Route::post('orders/{order}/notes', [App\Http\Controllers\Admin\OrderController::class, 'saveNotes'])->name('orders.notes');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    Route::post('orders/bulk-status', [App\Http\Controllers\Admin\OrderController::class, 'bulkStatus'])->name('orders.bulk-status');
    
    
    // Users
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/customers', [App\Http\Controllers\Admin\UserController::class, 'customers'])->name('users.customers');
    Route::get('/users/staff', [App\Http\Controllers\Admin\UserController::class, 'staff'])->name('users.staff');
    Route::get('/users/admins', [App\Http\Controllers\Admin\UserController::class, 'admins'])->name('users.admins');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-delete', [App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    
    // Coupon 
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);
    Route::get('/coupons/generate-code', [App\Http\Controllers\Admin\CouponController::class, 'generateCode'])->name('coupons.generate');
    Route::post('/coupons/{coupon}/toggle-status', [App\Http\Controllers\Admin\CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::post('/coupons/{coupon}/duplicate', [App\Http\Controllers\Admin\CouponController::class, 'duplicate'])->name('coupons.duplicate');
    Route::post('/coupons/bulk-delete', [App\Http\Controllers\Admin\CouponController::class, 'bulkDelete'])->name('coupons.bulk-delete');
    
    // Banners    
    Route::resource('banners', App\Http\Controllers\Admin\BannerController::class);
    Route::post('/banners/update-priority', [App\Http\Controllers\Admin\BannerController::class, 'updatePriority'])->name('banners.update-priority');
    Route::post('/banners/{banner}/toggle-status', [App\Http\Controllers\Admin\BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    Route::post('/banners/bulk-delete', [App\Http\Controllers\Admin\BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
    
    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\SettingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('store'); 
        Route::post('/update', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        Route::get('/{setting}/edit', [App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('edit');
        Route::put('/{setting}', [App\Http\Controllers\Admin\SettingController::class, 'updateSetting'])->name('update-setting');
        Route::delete('/{setting}', [App\Http\Controllers\Admin\SettingController::class, 'destroy'])->name('destroy');
        Route::post('/maintenance', [App\Http\Controllers\Admin\SettingController::class, 'maintenance'])->name('maintenance');
        Route::post('/cache', [App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('cache');
        Route::post('/backup', [App\Http\Controllers\Admin\SettingController::class, 'backup'])->name('backup');
    });
    
    // Reports
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/customers', [App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('reports.customers');
    Route::get('reports/export/{type}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
    
    // Courier Management
    Route::get('couriers', [App\Http\Controllers\Admin\CourierController::class, 'index'])->name('couriers.index');
    Route::get('couriers/create', [App\Http\Controllers\Admin\CourierController::class, 'create'])->name('couriers.create');
    Route::post('couriers', [App\Http\Controllers\Admin\CourierController::class, 'store'])->name('couriers.store');
    Route::get('couriers/{courier}/edit', [App\Http\Controllers\Admin\CourierController::class, 'edit'])->name('couriers.edit');
    Route::put('couriers/{courier}', [App\Http\Controllers\Admin\CourierController::class, 'update'])->name('couriers.update');
    Route::post('couriers/{code}/ship', [App\Http\Controllers\Admin\CourierController::class, 'createShipment'])->name('couriers.ship');
    Route::get('couriers/track/{trackingId}', [App\Http\Controllers\Admin\CourierController::class, 'trackShipment'])->name('couriers.track');

    // Product Variants
    Route::get('products/{product}/variants', [App\Http\Controllers\Admin\ProductVariantController::class, 'index'])->name('products.variants');
    Route::get('products/{product}/variants/create', [App\Http\Controllers\Admin\ProductVariantController::class, 'create'])->name('products.variants.create');
    Route::post('products/{product}/variants', [App\Http\Controllers\Admin\ProductVariantController::class, 'store'])->name('products.variants.store');
    Route::get('products/{product}/variants/{variant}/edit', [App\Http\Controllers\Admin\ProductVariantController::class, 'edit'])->name('products.variants.edit');
    Route::put('products/{product}/variants/{variant}', [App\Http\Controllers\Admin\ProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
    Route::post('products/{product}/variants/update-stock', [App\Http\Controllers\Admin\ProductVariantController::class, 'updateStock'])->name('products.variants.update-stock');

    // Attributes Management
    Route::resource('attributes', App\Http\Controllers\Admin\AttributeController::class);
    Route::post('attributes/update-order', [App\Http\Controllers\Admin\AttributeController::class, 'updateOrder'])->name('attributes.update-order');

    // Attribute Values Management (nested under attributes)
    Route::prefix('attributes/{attribute}/values')->name('attributes.values.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AttributeValueController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AttributeValueController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AttributeValueController::class, 'store'])->name('store');
        Route::get('/{value}/edit', [App\Http\Controllers\Admin\AttributeValueController::class, 'edit'])->name('edit');
        Route::put('/{value}', [App\Http\Controllers\Admin\AttributeValueController::class, 'update'])->name('update');
        Route::delete('/{value}', [App\Http\Controllers\Admin\AttributeValueController::class, 'destroy'])->name('destroy');
        Route::post('/update-order', [App\Http\Controllers\Admin\AttributeValueController::class, 'updateOrder'])->name('update-order');
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Admin\InventoryController::class, 'export'])->name('export');
        Route::get('/{product}', [App\Http\Controllers\Admin\InventoryController::class, 'show'])->name('show');
        Route::put('/update-stock/{variant}', [App\Http\Controllers\Admin\InventoryController::class, 'updateStock'])->name('update-stock');
        Route::post('/bulk-update', [App\Http\Controllers\Admin\InventoryController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/low-stock', [App\Http\Controllers\Admin\InventoryController::class, 'lowStock'])->name('low-stock');
    });

    // Reviews Management
    // Route::resource('reviews', App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'show', 'destroy']);
    // Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    // Route::post('reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    // Route::post('reviews/bulk-approve', [App\Http\Controllers\Admin\ReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');

    // Newsletter Subscribers
    Route::get('newsletter', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletter.index');
    Route::delete('newsletter/{subscriber}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');
    Route::post('newsletter/import', [App\Http\Controllers\Admin\NewsletterController::class, 'import'])->name('newsletter.import');
    Route::post('newsletter/export', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('newsletter.export');

    // System Tools
    // Route::prefix('system')->name('system.')->group(function () {
    //     Route::get('logs', [App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('logs');
    //     Route::get('phpinfo', [App\Http\Controllers\Admin\SystemController::class, 'phpinfo'])->name('phpinfo');
    //     Route::post('clear-cache', [App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('clear-cache');
    //     Route::post('optimize', [App\Http\Controllers\Admin\SystemController::class, 'optimize'])->name('optimize');
    //     Route::get('backups', [App\Http\Controllers\Admin\SystemController::class, 'backups'])->name('backups');
    //     Route::post('backup/create', [App\Http\Controllers\Admin\SystemController::class, 'createBackup'])->name('backup.create');
    //     Route::get('backup/download/{file}', [App\Http\Controllers\Admin\SystemController::class, 'downloadBackup'])->name('backup.download');
    //     Route::delete('backup/{file}', [App\Http\Controllers\Admin\SystemController::class, 'deleteBackup'])->name('backup.delete');
    // });

    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('profile/show', [App\Http\Controllers\Admin\ProfileController::class, 'viewProfile'])->name('profile.show');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/avatar', [App\Http\Controllers\Admin\ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::delete('profile/avatar', [App\Http\Controllers\Admin\ProfileController::class, 'avatarDestroy'])->name('profile.avatar.destroy');

    // Attribute Values Management
    Route::prefix('attribute-values')->name('attribute-values.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AttributeValueController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AttributeValueController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AttributeValueController::class, 'store'])->name('store');       
    });
        // Shipping Methods Management
        Route::prefix('shipping')->name('shipping.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ShippingMethodController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\ShippingMethodController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ShippingMethodController::class, 'store'])->name('store');
            Route::get('/{shippingMethod}/edit', [App\Http\Controllers\Admin\ShippingMethodController::class, 'edit'])->name('edit');
            Route::put('/{shippingMethod}', [App\Http\Controllers\Admin\ShippingMethodController::class, 'update'])->name('update');
            Route::delete('/{shippingMethod}', [App\Http\Controllers\Admin\ShippingMethodController::class, 'destroy'])->name('destroy');
            Route::post('/{shippingMethod}/toggle-status', [App\Http\Controllers\Admin\ShippingMethodController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/update-order', [App\Http\Controllers\Admin\ShippingMethodController::class, 'updateOrder'])->name('update-order');
        });
        
        // Payment Methods Management
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\PaymentMethodController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\PaymentMethodController::class, 'store'])->name('store');
            Route::get('/{paymentMethod}/edit', [App\Http\Controllers\Admin\PaymentMethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentMethod}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('update');
            Route::delete('/{paymentMethod}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'destroy'])->name('destroy');
            Route::post('/{paymentMethod}/toggle-status', [App\Http\Controllers\Admin\PaymentMethodController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/update-order', [App\Http\Controllers\Admin\PaymentMethodController::class, 'updateOrder'])->name('update-order');
        });

        // Newsletter Subscribers
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
            Route::delete('/{id}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('destroy');
            Route::get('/export', [App\Http\Controllers\Admin\NewsletterController::class, 'export'])->name('export');
            Route::post('/{id}/toggle', [App\Http\Controllers\Admin\NewsletterController::class, 'toggleStatus'])->name('toggle');
            Route::post('/bulk-delete', [App\Http\Controllers\Admin\NewsletterController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/unsubscribe/{email}', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
        });

        // Newsletter Campaigns
        Route::prefix('newsletter/campaigns')->name('newsletter.campaigns.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'store'])->name('store');
            Route::get('/{campaign}', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'show'])->name('show');
            Route::get('/{campaign}/progress', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'progress'])->name('progress');
            Route::post('/{campaign}/send', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'sendNow'])->name('send');
            Route::post('/{campaign}/cancel', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'cancel'])->name('cancel');
            Route::delete('/{campaign}', [App\Http\Controllers\Admin\NewsletterCampaignController::class, 'destroy'])->name('destroy');
            
        });

        // Contact Messages 
        Route::resource('contact-messages', App\Http\Controllers\Admin\ContactMessageController::class)->only(['index', 'show', 'destroy']);
        Route::post('contact-messages/{contactMessage}/reply', [App\Http\Controllers\Admin\ContactMessageController::class, 'reply'])->name('contact-messages.reply');
        Route::post('contact-messages/bulk-delete', [App\Http\Controllers\Admin\ContactMessageController::class, 'bulkDelete'])->name('contact-messages.bulk-delete');
        Route::post('contact-messages/{contactMessage}/toggle-read', [App\Http\Controllers\Admin\ContactMessageController::class, 'toggleRead'])->name('contact-messages.toggle-read');

        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [App\Http\Controllers\Admin\NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/list', [App\Http\Controllers\Admin\NotificationController::class, 'getNotifications'])->name('list');
            Route::post('/{id}/mark-as-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::post('/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('mark-all-read');
            Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [App\Http\Controllers\Admin\NotificationController::class, 'bulkDelete'])->name('bulk-delete');
            Route::delete('/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('clear-all');
        });   
        
    Route::resource('staff', App\Http\Controllers\Admin\StaffController::class);
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);

});



// AUTHENTICATION ROUTES (Breeze)

require __DIR__.'/auth.php';