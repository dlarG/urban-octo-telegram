<?php
namespace Database\Seeders;

use App\Enums\AmenityCategory;
use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $list = [
            ['Wi-Fi',            AmenityCategory::Connectivity, 'wifi'],
            ['Cable TV',         AmenityCategory::Connectivity, 'tv'],
            ['Aircon',           AmenityCategory::Utility,      'snowflake'],
            ['Water heater',     AmenityCategory::Utility,      'droplet'],
            ['Sub-metered power',AmenityCategory::Utility,      'bolt'],
            ['Generator backup', AmenityCategory::Utility,      'battery'],
            ['CCTV',             AmenityCategory::Safety,       'camera'],
            ['24/7 security',    AmenityCategory::Safety,       'shield'],
            ['Fire extinguisher',AmenityCategory::Safety,       'fire'],
            ['Study area',       AmenityCategory::Lifestyle,    'book'],
            ['Kitchen access',   AmenityCategory::Lifestyle,    'chef'],
            ['Laundry area',     AmenityCategory::Lifestyle,    'shirt'],
            ['Parking',          AmenityCategory::Lifestyle,    'car'],
        ];

        foreach ($list as [$name, $category, $icon]) {
            Amenity::updateOrCreate(
                ['name' => $name],
                ['category' => $category, 'icon_key' => $icon]
            );
        }
    }
}