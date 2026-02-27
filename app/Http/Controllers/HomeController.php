<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Review;
use App\Models\User;
use App\Models\NewsletterSubscriber;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessageNotification;
use App\Services\Seo\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index()
    {
        try {
            // Set SEO for homepage
            $this->seoService->setHomePage();

            // Get hero banners
            $heroBanners = Banner::position('home_hero')
                ->active()
                ->orderBy('priority', 'desc')
                ->get();

            // Get sidebar banners
            $sidebarBanners = Banner::position('home_sidebar')
                ->active()
                ->orderBy('priority', 'desc')
                ->limit(8)
                ->get();

            // Get bottom banners
            $promoBanners = Banner::position('home_bottom')
                ->active()
                ->orderBy('priority', 'desc')
                ->limit(5)
                ->get();

            // Get featured categories
            $featuredCategories = $this->getFeaturedCategories();

            // Get featured products
            $featuredProducts = Product::with(['brand', 'images', 'reviews'])
                ->where('is_featured', true)
                ->where('status', 'active')
                ->latest()
                ->limit(8)
                ->get();

            // If no featured products, get any active products
            if ($featuredProducts->isEmpty()) {
                $featuredProducts = Product::with(['brand', 'images'])
                    ->where('status', 'active')
                    ->inRandomOrder()
                    ->limit(8)
                    ->get();
            }

            // Get trending products
            $trendingProducts = Product::with(['brand', 'images', 'reviews'])
                ->where('is_trending', true)
                ->where('status', 'active')
                ->latest()
                ->limit(8)
                ->get();

            // If no trending products, get products with sale
            if ($trendingProducts->isEmpty()) {
                $trendingProducts = Product::with(['brand', 'images'])
                    ->where('status', 'active')
                    ->whereNotNull('sale_price')
                    ->whereColumn('sale_price', '<', 'base_price')
                    ->inRandomOrder()
                    ->limit(8)
                    ->get();
            }

            // Get new arrivals
            $newProducts = Product::with(['brand', 'images', 'reviews'])
                ->where('status', 'active')
                ->latest()
                ->limit(8)
                ->get();

            // Get top brands
            $brands = Brand::where('status', 1)
                ->orderBy('is_featured', 'desc')
                ->orderBy('name')
                ->limit(12)
                ->get();

            return view('storefront.home', compact(
                'heroBanners',
                'sidebarBanners',
                'featuredCategories',
                'featuredProducts',
                'trendingProducts',
                'newProducts',
                'promoBanners',
                'brands'
            ));

        } catch (\Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return view with empty collections on error
            return view('storefront.home', [
                'heroBanners' => collect([]),
                'sidebarBanners' => collect([]),
                'featuredCategories' => collect([]),
                'featuredProducts' => collect([]),
                'trendingProducts' => collect([]),
                'newProducts' => collect([]),
                'promoBanners' => collect([]),
                'brands' => collect([])
            ]);
        }
    }

    /**
     * Get featured categories safely
     */
    protected function getFeaturedCategories()
    {
        try {
            // Check if is_featured column exists
            $columns = DB::getSchemaBuilder()->getColumnListing('categories');
            
            if (in_array('is_featured', $columns)) {
                return Category::withCount('products')
                    ->where('is_featured', true)
                    ->where('status', 1)
                    ->orderBy('sort_order')
                    ->limit(6)
                    ->get();
            }
            
            // Fallback: get top level categories by sort order
            return Category::withCount('products')
                ->whereNull('parent_id')
                ->where('status', 1)
                ->orderBy('sort_order')
                ->limit(6)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Failed to get featured categories: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Search products (quick search for navbar)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $products = Product::with(['brand', 'images'])
                ->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orWhere('sku', 'LIKE', "%{$query}%")
                ->where('status', 'active')
                ->limit(10)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->sale_price ?? $product->base_price,
                        'image' => $product->images->first()->url ?? null,
                        'brand' => $product->brand->name ?? null
                    ];
                });

            return response()->json($products);
            
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Show about us page
     */
    public function about()
    {
        $this->seoService->setStaticPage('about');
        return view('storefront.pages.about');
    }

    /**
     * Show contact page
     */
    public function contact()
    {
        return view('storefront.pages.contact');
    }

    // public function contactSubmit(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'subject' => 'required|string|max:255',
    //         'message' => 'required|string',
    //     ]);

    //     try {
    //         // Store in database
    //         $contactMessage = ContactMessage::create($validated);

    //         // Send notification to admins - এইটা কাজ করবে এখন
    //         $this->sendNotificationToAdmins($contactMessage);

    //         return redirect()->route('contact')
    //             ->with('success', 'Thank you for contacting us! We will get back to you soon.');
                
    //     } catch (\Exception $e) {
    //         Log::error('Contact form error: ' . $e->getMessage());
            
    //         return redirect()->route('contact')
    //             ->with('error', 'Sorry, something went wrong. Please try again later.');
    //     }
    // }

    /**
     * Handle newsletter subscription
     */
    public function newsletterSubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            NewsletterSubscriber::create([
                'email' => $request->email,
                'is_active' => true,
                'verified_at' => now()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully subscribed to our newsletter!'
                ]);
            }

            return redirect()->back()->with('success', 'Successfully subscribed to our newsletter!');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to subscribe. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to subscribe. Please try again.')
                ->withInput();
        }
    }

    /**
     * Change language
     */
    public function changeLanguage($locale)
    {
        if (!in_array($locale, ['en', 'bn'])) {
            $locale = 'en';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return redirect()->back();
    }

    /**
     * Change currency
     */
    public function changeCurrency($currency)
    {
        if (!in_array($currency, ['BDT', 'USD'])) {
            $currency = 'BDT';
        }

        session(['currency' => $currency]);

        return redirect()->back();
    }

    /**
     * Show FAQ page
     */
    public function faq()
    {
        $this->seoService->setStaticPage('faq');
        return view('storefront.pages.faq');
    }

    public function terms()
    {
        $this->seoService->setStaticPage('terms');
        return view('storefront.pages.terms');
    }

    public function privacy()
    {
        $this->seoService->setStaticPage('privacy');
        return view('storefront.pages.privacy');
    }

    public function returns()
    {
        $this->seoService->setStaticPage('returns');
        return view('storefront.pages.returns');
    }

    protected function shareCartCount()
    {
        $cartCount = 0;
        
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->first();
            if ($cart) {
                $cartCount = $cart->items()->sum('quantity');
            }
        } else {
            $sessionId = session()->getId();
            $cart = Cart::where('session_id', $sessionId)->first();
            if ($cart) {
                $cartCount = $cart->items()->sum('quantity');
            }
        }
        
        View::share('cartCount', $cartCount);
    }

    /**
     * Share wishlist count with all views
     */
    protected function shareWishlistCount()
    {
        $wishlistCount = 0;
        
        if (auth()->check()) {
            $wishlistCount = Wishlist::where('user_id', auth()->id())->count();
        } else {
            $sessionId = session()->getId();
            $wishlistCount = Wishlist::where('session_id', $sessionId)->count();
        }
        
        View::share('wishlistCount', $wishlistCount);
    }

    /**
     * Share compare count with all views
     */
    protected function shareCompareCount()
    {
        $compareCount = 0;
        
        if (auth()->check()) {
            $compareCount = Compare::where('user_id', auth()->id())->count();
        } else {
            $sessionId = session()->getId();
            $compareCount = Compare::where('session_id', $sessionId)->count();
        }
        
        View::share('compareCount', $compareCount);
    }

    /**
     * Share categories with all views
     */
    protected function shareCategories()
    {
        $categories = \App\Models\Category::with('children')
            ->whereNull('parent_id')
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();
        
        View::share('categories', $categories);
    }
    // app/Http/Controllers/HomeController.php

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Store in database
            $contactMessage = ContactMessage::create($validated);
            
            // DEBUG: check if message is created
            \Log::info('Contact message created:', ['id' => $contactMessage->id, 'name' => $contactMessage->name]);
            
            // Send notification to admins
            $result = $this->sendNotificationToAdmins($contactMessage);
            
            // DEBUG: check notification result
            \Log::info('Notification send result:', ['result' => $result]);

            return redirect()->route('contact')
                ->with('success', 'Thank you for contacting us! We will get back to you soon.');
                
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('contact')
                ->with('error', 'Sorry, something went wrong. Please try again later.');
        }
    }

    private function sendNotificationToAdmins($contactMessage)
    {
        try {
            // Get admin user IDs
            $adminUserIds = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['super-admin', 'admin'])
                ->pluck('role_user.user_id');
            
            \Log::info('Admin IDs found:', ['ids' => $adminUserIds, 'count' => $adminUserIds->count()]);
            
            if ($adminUserIds->isEmpty()) {
                \Log::warning('No admin users found!');
                return false;
            }
            
            // Get admin users
            $admins = User::whereIn('id', $adminUserIds)->get();
            \Log::info('Admin users found:', ['count' => $admins->count()]);
            
            // Send notification to each admin
            foreach ($admins as $admin) {
                \Log::info('Sending to admin:', ['email' => $admin->email]);
                $admin->notify(new NewContactMessageNotification($contactMessage));
            }
            
            \Log::info('All notifications sent successfully');
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Failed to send notifications: ' . $e->getMessage());
            return false;
        }
    }
    
}