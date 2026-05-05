<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Cháy nổ', 'color_code' => '#FF0000', 'description' => 'Sự cố hỏa hoạn, cháy nhà, cháy rừng'],
            ['name' => 'Tai nạn giao thông', 'color_code' => '#FFA500', 'description' => 'Tai nạn xe cộ, va chạm giao thông'],
            ['name' => 'Trộm cắp', 'color_code' => '#800080', 'description' => 'Trộm cắp tài sản, cướp giật'],
            ['name' => 'Cây đổ', 'color_code' => '#008000', 'description' => 'Cây xanh đổ, cành cây gãy'],
            ['name' => 'Ngập lụt', 'color_code' => '#0000FF', 'description' => 'Ngập nước, úng lụt do mưa lớn'],
            ['name' => 'Khác', 'color_code' => '#808080', 'description' => 'Các sự cố khác'],
        ];

        foreach ($categories as $category) {
            DB::table('incident_categories')->insert([
                'name' => $category['name'],
                'color_code' => $category['color_code'],
                'description' => $category['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}