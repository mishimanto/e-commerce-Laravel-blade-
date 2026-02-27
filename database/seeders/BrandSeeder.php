<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            // Smartphone Brands
            ['name' => 'Apple', 'slug' => 'apple', 'website' => 'https://www.apple.com', 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Samsung', 'slug' => 'samsung', 'website' => 'https://www.samsung.com', 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'website' => 'https://www.mi.com', 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'OnePlus', 'slug' => 'oneplus', 'website' => 'https://www.oneplus.com', 'is_featured' => true, 'sort_order' => 4],
            ['name' => 'Google', 'slug' => 'google', 'website' => 'https://store.google.com', 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'Oppo', 'slug' => 'oppo', 'website' => 'https://www.oppo.com', 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Vivo', 'slug' => 'vivo', 'website' => 'https://www.vivo.com', 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Realme', 'slug' => 'realme', 'website' => 'https://www.realme.com', 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Nokia', 'slug' => 'nokia', 'website' => 'https://www.nokia.com', 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Sony', 'slug' => 'sony', 'website' => 'https://www.sony.com', 'is_featured' => false, 'sort_order' => 10],
            
            // Laptop Brands
            ['name' => 'Dell', 'slug' => 'dell', 'website' => 'https://www.dell.com', 'is_featured' => true, 'sort_order' => 11],
            ['name' => 'HP', 'slug' => 'hp', 'website' => 'https://www.hp.com', 'is_featured' => true, 'sort_order' => 12],
            ['name' => 'Lenovo', 'slug' => 'lenovo', 'website' => 'https://www.lenovo.com', 'is_featured' => true, 'sort_order' => 13],
            ['name' => 'Asus', 'slug' => 'asus', 'website' => 'https://www.asus.com', 'is_featured' => true, 'sort_order' => 14],
            ['name' => 'Acer', 'slug' => 'acer', 'website' => 'https://www.acer.com', 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'MSI', 'slug' => 'msi', 'website' => 'https://www.msi.com', 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'Razer', 'slug' => 'razer', 'website' => 'https://www.razer.com', 'is_featured' => false, 'sort_order' => 17],
            
            // Audio Brands
            ['name' => 'Bose', 'slug' => 'bose', 'website' => 'https://www.bose.com', 'is_featured' => true, 'sort_order' => 18],
            ['name' => 'Sony', 'slug' => 'sony-audio', 'website' => 'https://www.sony.com', 'is_featured' => true, 'sort_order' => 19],
            ['name' => 'JBL', 'slug' => 'jbl', 'website' => 'https://www.jbl.com', 'is_featured' => true, 'sort_order' => 20],
            ['name' => 'Sennheiser', 'slug' => 'sennheiser', 'website' => 'https://www.sennheiser.com', 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Audio-Technica', 'slug' => 'audio-technica', 'website' => 'https://www.audio-technica.com', 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Beats', 'slug' => 'beats', 'website' => 'https://www.beatsbydre.com', 'is_featured' => false, 'sort_order' => 23],
            
            // Wearable Brands
            ['name' => 'Apple Watch', 'slug' => 'apple-watch', 'website' => 'https://www.apple.com/watch', 'is_featured' => true, 'sort_order' => 24],
            ['name' => 'Samsung Watch', 'slug' => 'samsung-watch', 'website' => 'https://www.samsung.com/watches', 'is_featured' => true, 'sort_order' => 25],
            ['name' => 'Fitbit', 'slug' => 'fitbit', 'website' => 'https://www.fitbit.com', 'is_featured' => false, 'sort_order' => 26],
            ['name' => 'Garmin', 'slug' => 'garmin', 'website' => 'https://www.garmin.com', 'is_featured' => false, 'sort_order' => 27],
            
            // Accessory Brands
            ['name' => 'Anker', 'slug' => 'anker', 'website' => 'https://www.anker.com', 'is_featured' => true, 'sort_order' => 28],
            ['name' => 'Belkin', 'slug' => 'belkin', 'website' => 'https://www.belkin.com', 'is_featured' => false, 'sort_order' => 29],
            ['name' => 'Spigen', 'slug' => 'spigen', 'website' => 'https://www.spigen.com', 'is_featured' => false, 'sort_order' => 30],
            ['name' => 'UAG', 'slug' => 'uag', 'website' => 'https://www.urbanarmorgear.com', 'is_featured' => false, 'sort_order' => 31],
            ['name' => 'OtterBox', 'slug' => 'otterbox', 'website' => 'https://www.otterbox.com', 'is_featured' => false, 'sort_order' => 32],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'description' => "Official {$brand['name']} products",
                'website' => $brand['website'],
                'is_featured' => $brand['is_featured'],
                'status' => true,
                'sort_order' => $brand['sort_order'],
            ]);
        }

        $this->command->info('Brands seeded successfully!');
    }
}