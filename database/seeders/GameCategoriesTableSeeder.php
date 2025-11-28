<?php
/**
 * Copyright (c) 2025 FPT University
 *
 * @author    Phạm Hoàng Tuấn
 * @email     phamhoangtuanqn@gmail.com
 * @facebook  fb.com/phamhoangtuanqn
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'NICK HỒI SINH NGỌC RỒNG',
                'slug' => Str::slug('NICK HỒI SINH NGỌC RỒNG'),
                'thumbnail' => 'https://i.postimg.cc/qq3pynYh/20240215164859nickhsnr.jpg',
                'description' => 'Danh mục tài khoản hồi sinh trong Ngọc Rồng Online.',
                'type' => 'standard',
            ],
            [
                'name' => 'SƠ SINH + WIN DOANH TRẠI + ZIN',
                'slug' => Str::slug('SƠ SINH + WIN DOANH TRẠI + ZIN'),
                'thumbnail' => 'https://i.postimg.cc/qq3pynYh/20240215164859nickhsnr.jpg',
                'description' => 'Danh mục tài khoản sơ sinh với khả năng chiến thắng trong doanh trại.',
                'type' => 'standard',
            ],
            [
                'name' => 'NGỌC RỒNG NICK TẦM TRUNG',
                'slug' => Str::slug('NGỌC RỒNG NICK TẦM TRUNG'),
                'thumbnail' => 'https://i.postimg.cc/qq3pynYh/20240215164859nickhsnr.jpg',
                'description' => 'Danh mục tài khoản Ngọc Rồng tầm trung, phù hợp cho người chơi mới.',
                'type' => 'standard',
            ],
            [
                'name' => 'SHOP VẬT PHẨM',
                'slug' => Str::slug('SHOP VẬT PHẨM'),
                'thumbnail' => 'https://i.postimg.cc/Jzc4pNhj/NRO-ITEM.png',
                'description' => 'Danh mục cửa hàng vật phẩm trong Ngọc Rồng Online.',
                'type' => 'standard',
            ],
            [
                'name' => 'NICK CÓ ĐỆ SKILL 2 + THOÁT SƠ SINH',
                'slug' => Str::slug('NICK CÓ ĐỆ SKILL 2 + THOÁT SƠ SINH'),
                'thumbnail' => 'https://i.postimg.cc/Jzc4pNhj/NRO-ITEM.png',
                'description' => 'Danh mục tài khoản có đệ skill 2, phù hợp cho người chơi nâng cao.',
                'type' => 'standard',
            ],
            [
                'name' => 'ACC TRẮNG GARENA',
                'slug' => 'acc-white',
                'thumbnail' => 'https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:format(webp):quality(75)/2024_1_15_638409359797732374_tai-garena-pc-1.jpg',
                'description' => 'Danh mục đặc biệt cho acc trắng/reroll (user & mật khẩu).',
                'type' => 'white_accounts',
            ],
        ];

        foreach ($categories as $category) {
            $exists = DB::table('game_categories')->where('slug', $category['slug'])->exists();

            if ($exists) {
                DB::table('game_categories')
                    ->where('slug', $category['slug'])
                    ->update(array_merge($category, [
                        'active' => true,
                        'updated_at' => now(),
                    ]));
            } else {
                DB::table('game_categories')->insert(array_merge($category, [
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
