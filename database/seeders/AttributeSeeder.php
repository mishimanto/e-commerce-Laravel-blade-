<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================== COLOR ATTRIBUTE ====================
        $colorAttribute = Attribute::create([
            'name' => 'Color',
            'slug' => 'color',
            'type' => 'color',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 1
        ]);

        $colors = [
            ['value' => 'Red', 'color_code' => '#FF0000', 'sort_order' => 1],
            ['value' => 'Blue', 'color_code' => '#0000FF', 'sort_order' => 2],
            ['value' => 'Green', 'color_code' => '#00FF00', 'sort_order' => 3],
            ['value' => 'Black', 'color_code' => '#000000', 'sort_order' => 4],
            ['value' => 'White', 'color_code' => '#FFFFFF', 'sort_order' => 5],
            ['value' => 'Yellow', 'color_code' => '#FFFF00', 'sort_order' => 6],
            ['value' => 'Purple', 'color_code' => '#800080', 'sort_order' => 7],
            ['value' => 'Orange', 'color_code' => '#FFA500', 'sort_order' => 8],
            ['value' => 'Pink', 'color_code' => '#FFC0CB', 'sort_order' => 9],
            ['value' => 'Brown', 'color_code' => '#A52A2A', 'sort_order' => 10],
            ['value' => 'Gray', 'color_code' => '#808080', 'sort_order' => 11],
            ['value' => 'Silver', 'color_code' => '#C0C0C0', 'sort_order' => 12],
            ['value' => 'Gold', 'color_code' => '#FFD700', 'sort_order' => 13],
        ];

        foreach ($colors as $color) {
            AttributeValue::create([
                'attribute_id' => $colorAttribute->id,
                'value' => $color['value'],
                'slug' => Str::slug($color['value']),
                'color_code' => $color['color_code'],
                'sort_order' => $color['sort_order']
            ]);
        }

        // ==================== SIZE ATTRIBUTE ====================
        $sizeAttribute = Attribute::create([
            'name' => 'Size',
            'slug' => 'size',
            'type' => 'size',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 2
        ]);

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];
        
        foreach ($sizes as $index => $size) {
            AttributeValue::create([
                'attribute_id' => $sizeAttribute->id,
                'value' => $size,
                'slug' => Str::slug($size),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== STORAGE ATTRIBUTE (For Phones/Electronics) ====================
        $storageAttribute = Attribute::create([
            'name' => 'Storage',
            'slug' => 'storage',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 3
        ]);

        $storages = ['16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB'];
        
        foreach ($storages as $index => $storage) {
            AttributeValue::create([
                'attribute_id' => $storageAttribute->id,
                'value' => $storage,
                'slug' => Str::slug($storage),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== RAM ATTRIBUTE (For Phones/Computers) ====================
        $ramAttribute = Attribute::create([
            'name' => 'RAM',
            'slug' => 'ram',
            'type' => 'select',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 4
        ]);

        $rams = ['2GB', '4GB', '6GB', '8GB', '12GB', '16GB', '32GB', '64GB'];
        
        foreach ($rams as $index => $ram) {
            AttributeValue::create([
                'attribute_id' => $ramAttribute->id,
                'value' => $ram,
                'slug' => Str::slug($ram),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== PROCESSOR ATTRIBUTE ====================
        $processorAttribute = Attribute::create([
            'name' => 'Processor',
            'slug' => 'processor',
            'type' => 'select',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 5
        ]);

        $processors = [
            'Intel Core i3',
            'Intel Core i5',
            'Intel Core i7',
            'Intel Core i9',
            'AMD Ryzen 3',
            'AMD Ryzen 5',
            'AMD Ryzen 7',
            'AMD Ryzen 9',
            'Apple M1',
            'Apple M2',
            'Apple M3',
            'Snapdragon 8 Gen 1',
            'Snapdragon 8 Gen 2',
            'MediaTek Dimensity 9000',
            'A16 Bionic',
            'A17 Pro'
        ];
        
        foreach ($processors as $index => $processor) {
            AttributeValue::create([
                'attribute_id' => $processorAttribute->id,
                'value' => $processor,
                'slug' => Str::slug($processor),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== BATTERY CAPACITY ATTRIBUTE ====================
        $batteryAttribute = Attribute::create([
            'name' => 'Battery Capacity',
            'slug' => 'battery-capacity',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 6
        ]);

        $batteries = ['3000mAh', '4000mAh', '5000mAh', '6000mAh', '7000mAh', '8000mAh'];
        
        foreach ($batteries as $index => $battery) {
            AttributeValue::create([
                'attribute_id' => $batteryAttribute->id,
                'value' => $battery,
                'slug' => Str::slug($battery),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== SCREEN SIZE ATTRIBUTE ====================
        $screenAttribute = Attribute::create([
            'name' => 'Screen Size',
            'slug' => 'screen-size',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 7
        ]);

        $screens = ['4.7"', '5.5"', '6.1"', '6.3"', '6.5"', '6.7"', '6.9"', '7.6"'];
        
        foreach ($screens as $index => $screen) {
            AttributeValue::create([
                'attribute_id' => $screenAttribute->id,
                'value' => $screen,
                'slug' => Str::slug($screen),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== MATERIAL ATTRIBUTE (For Accessories) ====================
        $materialAttribute = Attribute::create([
            'name' => 'Material',
            'slug' => 'material',
            'type' => 'checkbox',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 8
        ]);

        $materials = ['Leather', 'Silicone', 'Plastic', 'Metal', 'Wood', 'Fabric', 'Glass', 'Ceramic'];
        
        foreach ($materials as $index => $material) {
            AttributeValue::create([
                'attribute_id' => $materialAttribute->id,
                'value' => $material,
                'slug' => Str::slug($material),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== WARRANTY ATTRIBUTE ====================
        $warrantyAttribute = Attribute::create([
            'name' => 'Warranty Period',
            'slug' => 'warranty-period',
            'type' => 'select',
            'is_required' => false,
            'is_filterable' => false,
            'sort_order' => 9
        ]);

        $warranties = ['No Warranty', '6 Months', '1 Year', '2 Years', '3 Years', '5 Years'];
        
        foreach ($warranties as $index => $warranty) {
            AttributeValue::create([
                'attribute_id' => $warrantyAttribute->id,
                'value' => $warranty,
                'slug' => Str::slug($warranty),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== CONNECTIVITY ATTRIBUTE ====================
        $connectivityAttribute = Attribute::create([
            'name' => 'Connectivity',
            'slug' => 'connectivity',
            'type' => 'checkbox',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 10
        ]);

        $connectivities = ['WiFi', 'Bluetooth', 'NFC', '5G', '4G LTE', 'GPS', 'USB-C', 'Lightning'];
        
        foreach ($connectivities as $index => $connectivity) {
            AttributeValue::create([
                'attribute_id' => $connectivityAttribute->id,
                'value' => $connectivity,
                'slug' => Str::slug($connectivity),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== OPERATING SYSTEM ATTRIBUTE ====================
        $osAttribute = Attribute::create([
            'name' => 'Operating System',
            'slug' => 'operating-system',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 11
        ]);

        $oses = ['Android', 'iOS', 'Windows', 'macOS', 'Linux', 'HarmonyOS'];
        
        foreach ($oses as $index => $os) {
            AttributeValue::create([
                'attribute_id' => $osAttribute->id,
                'value' => $os,
                'slug' => Str::slug($os),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== CAMERA RESOLUTION ATTRIBUTE ====================
        $cameraAttribute = Attribute::create([
            'name' => 'Camera Resolution',
            'slug' => 'camera-resolution',
            'type' => 'select',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 12
        ]);

        $cameras = ['8MP', '12MP', '16MP', '24MP', '32MP', '48MP', '64MP', '108MP', '200MP'];
        
        foreach ($cameras as $index => $camera) {
            AttributeValue::create([
                'attribute_id' => $cameraAttribute->id,
                'value' => $camera,
                'slug' => Str::slug($camera),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== GENDER ATTRIBUTE (For Apparel) ====================
        $genderAttribute = Attribute::create([
            'name' => 'Gender',
            'slug' => 'gender',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 13
        ]);

        $genders = ['Men', 'Women', 'Unisex', 'Kids', 'Boys', 'Girls'];
        
        foreach ($genders as $index => $gender) {
            AttributeValue::create([
                'attribute_id' => $genderAttribute->id,
                'value' => $gender,
                'slug' => Str::slug($gender),
                'sort_order' => $index + 1
            ]);
        }

        // ==================== RATING ATTRIBUTE (For Reviews) ====================
        $ratingAttribute = Attribute::create([
            'name' => 'Customer Rating',
            'slug' => 'customer-rating',
            'type' => 'radio',
            'is_required' => false,
            'is_filterable' => true,
            'sort_order' => 14
        ]);

        $ratings = ['5 Stars', '4 Stars & Up', '3 Stars & Up'];
        
        foreach ($ratings as $index => $rating) {
            AttributeValue::create([
                'attribute_id' => $ratingAttribute->id,
                'value' => $rating,
                'slug' => Str::slug($rating),
                'sort_order' => $index + 1
            ]);
        }

        $this->command->info('Attributes seeded successfully!');
    }
}