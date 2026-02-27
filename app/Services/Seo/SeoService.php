<?php

namespace App\Services\Seo;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Page;
use App\Models\Setting;

class SeoService
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
    }

    public function setHomePage()
    {
        $title = $this->settings['meta_title'] ?? config('app.name');
        $description = $this->settings['meta_description'] ?? 'Your trusted store for phones and gadgets';
        $keywords = $this->settings['meta_keywords'] ?? 'phones, gadgets, electronics';

        $this->setMetaTags($title, $description, $keywords);
        $this->setOpenGraphTags($title, $description, asset('images/og-home.jpg'));
        $this->setTwitterCards($title, $description, asset('images/twitter-home.jpg'));
        $this->setSchemaOrg([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('product.search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ]);
    }

    public function setProductPage(Product $product)
    {
        $title = $product->meta_title ?: $product->name . ' - ' . config('app.name');
        $description = $product->meta_description ?: $product->short_description;
        $keywords = $product->meta_keywords;

        $this->setMetaTags($title, $description, $keywords);
        $this->setOpenGraphTags(
            $title, 
            $description, 
            $product->images->first()->url ?? asset('images/no-image.jpg'),
            $product->url ?? route('product.show', $product->slug)
        );
        $this->setTwitterCards($title, $description, $product->images->first()->url ?? asset('images/no-image.jpg'));

        // Product schema
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description,
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? 'Generic'
            ],
            'image' => $product->images->map->url->toArray(),
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'BDT',
                'price' => $product->sale_price ?? $product->base_price,
                'availability' => $product->stock > 0 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
                'priceValidUntil' => $product->sale_price ? now()->addDays(30)->format('Y-m-d') : null
            ]
        ];

        // Add aggregate rating if exists
        if ($product->reviews()->exists()) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->reviews->avg('rating'),
                'reviewCount' => $product->reviews->count()
            ];
        }

        $this->setSchemaOrg($schema);
    }

    public function setCategoryPage(Category $category)
    {
        $title = $category->meta_title ?: $category->name . ' - ' . config('app.name');
        $description = $category->meta_description ?: "Browse {$category->name} at " . config('app.name');
        $keywords = $category->meta_keywords;

        $this->setMetaTags($title, $description, $keywords);
        $this->setOpenGraphTags($title, $description, $category->image ? asset('storage/' . $category->image) : null);
        $this->setTwitterCards($title, $description, $category->image ? asset('storage/' . $category->image) : null);

        $this->setSchemaOrg([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $description,
            'url' => route('product.category', $category->slug)
        ]);
    }

    public function setBrandPage(Brand $brand)
    {
        $title = $brand->meta_title ?: $brand->name . ' - ' . config('app.name');
        $description = $brand->meta_description ?: "Shop {$brand->name} products at " . config('app.name');
        $keywords = $brand->meta_keywords;

        $this->setMetaTags($title, $description, $keywords);
        $this->setOpenGraphTags($title, $description, $brand->logo ? asset('storage/' . $brand->logo) : null);
        $this->setTwitterCards($title, $description, $brand->logo ? asset('storage/' . $brand->logo) : null);

        $this->setSchemaOrg([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $brand->name,
            'description' => $description,
            'url' => route('product.brand', $brand->slug)
        ]);
    }

    public function setSearchPage($keyword)
    {
        $title = "Search results for \"{$keyword}\" - " . config('app.name');
        $description = "Browse search results for {$keyword} at " . config('app.name');

        $this->setMetaTags($title, $description);
        $this->setOpenGraphTags($title, $description);
        $this->setTwitterCards($title, $description);
    }

    public function setCartPage()
    {
        $title = "Shopping Cart - " . config('app.name');
        $description = "Review your items before checkout";

        $this->setMetaTags($title, $description);
    }

    public function setCheckoutPage()
    {
        $title = "Checkout - " . config('app.name');
        $description = "Complete your purchase securely";

        $this->setMetaTags($title, $description);
    }

    public function setStaticPage($pageName)
    {
        $titles = [
            'about' => 'About Us',
            'contact' => 'Contact Us',
            'faq' => 'Frequently Asked Questions',
            'terms' => 'Terms and Conditions',
            'privacy' => 'Privacy Policy',
            'returns' => 'Return Policy'
        ];

        $title = $titles[$pageName] ?? ucfirst($pageName) . ' - ' . config('app.name');
        $description = "Learn more about our {$pageName} at " . config('app.name');

        $this->setMetaTags($title, $description);
        $this->setOpenGraphTags($title, $description);
        $this->setTwitterCards($title, $description);
    }

    protected function setMetaTags($title, $description, $keywords = null)
    {
        view()->share('meta_title', $title);
        view()->share('meta_description', $description);
        
        if ($keywords) {
            view()->share('meta_keywords', $keywords);
        }
    }

    protected function setOpenGraphTags($title, $description, $image = null, $url = null)
    {
        $tags = [
            'og:title' => $title,
            'og:description' => $description,
            'og:site_name' => config('app.name'),
            'og:type' => 'website',
            'og:url' => $url ?? url()->current(),
        ];

        if ($image) {
            $tags['og:image'] = $image;
        }

        view()->share('og_tags', $tags);
    }

    protected function setTwitterCards($title, $description, $image = null)
    {
        $tags = [
            'twitter:card' => $image ? 'summary_large_image' : 'summary',
            'twitter:title' => $title,
            'twitter:description' => $description,
            'twitter:site' => '@' . config('app.name'),
        ];

        if ($image) {
            $tags['twitter:image'] = $image;
        }

        view()->share('twitter_tags', $tags);
    }

    protected function setSchemaOrg($schema)
    {
        view()->share('schema_markup', $schema);
    }

    public function generateSitemap()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= $this->createUrlEntry(route('home'), now(), '1.0', 'daily');

        // Static pages
        $staticPages = ['about', 'contact', 'faq', 'terms', 'privacy', 'returns'];
        foreach ($staticPages as $page) {
            $sitemap .= $this->createUrlEntry(route($page), now(), '0.5', 'monthly');
        }

        // Categories
        $categories = Category::where('status', true)->get();
        foreach ($categories as $category) {
            $sitemap .= $this->createUrlEntry(
                route('product.category', $category->slug),
                $category->updated_at,
                '0.7',
                'weekly'
            );
        }

        // Brands
        $brands = Brand::where('status', true)->get();
        foreach ($brands as $brand) {
            $sitemap .= $this->createUrlEntry(
                route('product.brand', $brand->slug),
                $brand->updated_at,
                '0.6',
                'weekly'
            );
        }

        // Products
        $products = Product::where('status', 'active')->get();
        foreach ($products as $product) {
            $sitemap .= $this->createUrlEntry(
                route('product.show', $product->slug),
                $product->updated_at,
                '0.9',
                'daily'
            );
        }

        $sitemap .= '</urlset>';

        return $sitemap;
    }

    protected function createUrlEntry($loc, $lastmod, $priority, $changefreq)
    {
        $lastmod = $lastmod instanceof \DateTime ? $lastmod->format('Y-m-d') : $lastmod;
        
        return '<url>' .
               '<loc>' . $loc . '</loc>' .
               '<lastmod>' . $lastmod . '</lastmod>' .
               '<changefreq>' . $changefreq . '</changefreq>' .
               '<priority>' . $priority . '</priority>' .
               '</url>';
    }

    public function generateRobotsTxt()
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /cart/\n";
        $robots .= "Disallow: /checkout/\n";
        $robots .= "Disallow: /profile/\n";
        $robots .= "Disallow: /login\n";
        $robots .= "Disallow: /register\n";
        $robots .= "Disallow: /password-reset\n";
        $robots .= "Sitemap: " . route('sitemap') . "\n";

        return $robots;
    }

    public function getBreadcrumbSchema(array $items)
    {
        $itemList = [];
        
        foreach ($items as $index => $item) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList
        ];
    }

    public function setProductsPage($title = null)
    {
        $pageTitle = $title ?? 'All Products';
        
        $title = $pageTitle . ' - ' . config('app.name');
        $description = 'Browse our complete collection of products at ' . config('app.name') . '. Find the best deals on smartphones, gadgets, accessories and more.';
        $keywords = 'products, shopping, online store, gadgets, electronics';

        $this->setMetaTags($title, $description, $keywords);
        $this->setOpenGraphTags($title, $description, null, route('product.index'));
        $this->setTwitterCards($title, $description);

        $this->setSchemaOrg([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageTitle,
            'description' => $description,
            'url' => route('product.index')
        ]);
    }
}