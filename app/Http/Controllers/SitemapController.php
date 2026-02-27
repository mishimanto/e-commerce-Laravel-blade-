<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Page;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml
     */
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= $this->createUrlEntry(
            route('home'),
            now()->format('Y-m-d'),
            '1.0',
            'daily'
        );

        // Static pages
        $staticPages = [
            'about' => ['url' => route('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            'contact' => ['url' => route('contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            'faq' => ['url' => route('faq'), 'priority' => '0.4', 'changefreq' => 'monthly'],
            'terms' => ['url' => route('terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            'privacy' => ['url' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            'returns' => ['url' => route('returns'), 'priority' => '0.4', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->createUrlEntry(
                $page['url'],
                now()->format('Y-m-d'),
                $page['priority'],
                $page['changefreq']
            );
        }

        // Product listing pages
        $sitemap .= $this->createUrlEntry(
            route('product.index'),
            now()->format('Y-m-d'),
            '0.8',
            'daily'
        );

        // Categories
        $categories = Category::active()->get();
        foreach ($categories as $category) {
            $sitemap .= $this->createUrlEntry(
                route('product.category', $category->slug),
                $category->updated_at->format('Y-m-d'),
                '0.7',
                'weekly'
            );
        }

        // Brands
        $brands = Brand::active()->get();
        foreach ($brands as $brand) {
            $sitemap .= $this->createUrlEntry(
                route('product.brand', $brand->slug),
                $brand->updated_at->format('Y-m-d'),
                '0.6',
                'weekly'
            );
        }

        // Products
        $products = Product::active()->get();
        foreach ($products as $product) {
            $sitemap .= $this->createUrlEntry(
                route('product.show', $product->slug),
                $product->updated_at->format('Y-m-d'),
                '0.9',
                'daily'
            );
        }

        // Custom pages from database
        $pages = Page::where('is_published', true)->get();
        foreach ($pages as $page) {
            $sitemap .= $this->createUrlEntry(
                route('page.show', $page->slug),
                $page->updated_at->format('Y-m-d'),
                '0.5',
                'monthly'
            );
        }

        $sitemap .= '</urlset>';

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * Generate sitemap for images
     */
    public function images()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $sitemap .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        $products = Product::with('images')->active()->get();

        foreach ($products as $product) {
            $productUrl = route('product.show', $product->slug);
            
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $productUrl . '</loc>';
            $sitemap .= '<lastmod>' . $product->updated_at->format('Y-m-d') . '</lastmod>';
            $sitemap .= '<changefreq>daily</changefreq>';
            $sitemap .= '<priority>0.9</priority>';
            
            foreach ($product->images as $image) {
                $sitemap .= '<image:image>';
                $sitemap .= '<image:loc>' . $image->url . '</image:loc>';
                $sitemap .= '<image:title>' . htmlspecialchars($image->alt_text ?? $product->name) . '</image:title>';
                $sitemap .= '</image:image>';
            }
            
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * Generate sitemap index
     */
    public function indexSitemap()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $sitemaps = [
            'main' => route('sitemap.main'),
            'images' => route('sitemap.images'),
            'products' => route('sitemap.products'),
            'categories' => route('sitemap.categories'),
            'brands' => route('sitemap.brands'),
        ];

        foreach ($sitemaps as $name => $url) {
            $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $url . '</loc>';
            $sitemap .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
            $sitemap .= '</sitemap>';
        }

        $sitemap .= '</sitemapindex>';

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * Create a URL entry for sitemap
     */
    protected function createUrlEntry($loc, $lastmod, $priority, $changefreq)
    {
        return '<url>' .
               '<loc>' . $loc . '</loc>' .
               '<lastmod>' . $lastmod . '</lastmod>' .
               '<changefreq>' . $changefreq . '</changefreq>' .
               '<priority>' . $priority . '</priority>' .
               '</url>';
    }
}