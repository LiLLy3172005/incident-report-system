<?php
// database/seeders/IncidentCategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentCategorySeeder extends Seeder
{
    public function run()
    {
        // Xóa data cũ nếu có để tránh duplicate
        DB::table('incident_categories')->truncate();

        $categories = [
            // Thiên tai
            ['name' => 'Cháy nổ',               'color_code' => '#EF4444', 'description' => 'Sự cố hỏa hoạn, cháy nhà, cháy rừng, nổ khí gas'],
            ['name' => 'Ngập lụt',               'color_code' => '#3B82F6', 'description' => 'Ngập nước, úng lụt do mưa lớn hoặc triều cường'],
            ['name' => 'Sạt lở đất',             'color_code' => '#92400E', 'description' => 'Sạt lở đất đá, lũ quét, taluy sụp đổ'],
            ['name' => 'Cây đổ',                 'color_code' => '#16A34A', 'description' => 'Cây xanh đổ, cành cây gãy chắn đường hoặc đè người'],
            ['name' => 'Mưa đá / Lốc xoáy',     'color_code' => '#7C3AED', 'description' => 'Thiên tai mưa đá, gió lốc, lốc xoáy'],

            // Giao thông
            ['name' => 'Tai nạn giao thông',     'color_code' => '#F97316', 'description' => 'Tai nạn xe cộ, va chạm giao thông, người bị thương'],
            ['name' => 'Kẹt xe / Ùn tắc',        'color_code' => '#FBBF24', 'description' => 'Tắc đường nghiêm trọng, ùn ứ giao thông kéo dài'],
            ['name' => 'Đường hư hỏng',           'color_code' => '#6B7280', 'description' => 'Ổ gà, đường sụp lún, cống hở, nắp cống mất'],
            ['name' => 'Đèn tín hiệu hỏng',      'color_code' => '#D97706', 'description' => 'Đèn giao thông hỏng, biển báo ngã đổ'],

            // An ninh
            ['name' => 'Trộm cắp / Cướp giật',  'color_code' => '#DC2626', 'description' => 'Trộm cắp tài sản, giật đồ, cướp có vũ khí'],
            ['name' => 'Đánh nhau / Bạo lực',   'color_code' => '#B91C1C', 'description' => 'Ẩu đả, bạo lực, gây rối trật tự công cộng'],
            ['name' => 'Người mất tích',          'color_code' => '#0EA5E9', 'description' => 'Trẻ em hoặc người già lạc đường, mất tích'],

            // Hạ tầng
            ['name' => 'Mất điện',               'color_code' => '#78716C', 'description' => 'Cúp điện diện rộng, sự cố đường dây điện'],
            ['name' => 'Vỡ / Bể đường ống nước', 'color_code' => '#0891B2', 'description' => 'Vỡ ống nước, nước phun, ngập do ống bể'],
            ['name' => 'Rò rỉ khí gas',          'color_code' => '#CA8A04', 'description' => 'Mùi gas trong khu dân cư, nhà máy, bếp ăn'],
            ['name' => 'Ô nhiễm môi trường',     'color_code' => '#4D7C0F', 'description' => 'Xả thải bẩn, khói bụi, mùi hôi, ô nhiễm kênh rạch'],

            // Y tế
            ['name' => 'Người bất tỉnh / Cấp cứu', 'color_code' => '#E11D48', 'description' => 'Người ngã, bất tỉnh, đau tim, cần cấp cứu khẩn'],
            ['name' => 'Dịch bệnh',              'color_code' => '#6D28D9', 'description' => 'Nghi ngờ dịch bệnh, nhiều người có triệu chứng bất thường'],

            // Khác
            ['name' => 'Khác',                   'color_code' => '#9CA3AF', 'description' => 'Các sự cố khác chưa phân loại'],
        ];

        foreach ($categories as $i => $category) {
            DB::table('incident_categories')->insert([
                'name'        => $category['name'],
                'color_code'  => $category['color_code'],
                'description' => $category['description'],
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('✅ Đã tạo ' . count($categories) . ' loại sự cố!');
    }
}