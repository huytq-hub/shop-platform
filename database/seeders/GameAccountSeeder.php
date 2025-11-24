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

class GameAccountSeeder extends Seeder
{
    public function run()
    {
        DB::table('game_accounts')->insert([
            [
                'game_category_id' => 1,
                'account_name' => 'player1',
                'password' => '123456',
                'price' => 100000,
                'status' => 'available',
                'account_version' => 'vietnam',
                'login_method' => 'Garena',
                'team_value' => 120000000,
                'buyer_id' => null,
                'note' => 'UY TÍN 100%',
                'thumb' => 'https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg',
                'images' => json_encode([
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/8fa08a6fc12bf504857f4327a461f6f4.jpeg"
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_category_id' => 1,
                'account_name' => 'player2',
                'password' => '123456',
                'price' => 150000,
                'status' => 'sold',
                'account_version' => 'global',
                'login_method' => 'Facebook',
                'team_value' => 150000000,
                'buyer_id' => 3,
                'note' => 'UY TÍN 100%',
                'thumb' => 'https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg',
                'images' => json_encode([
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg"
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_category_id' => 1,
                'account_name' => 'player3',
                'password' => '123456',
                'price' => 200000,
                'status' => 'available',
                'account_version' => 'vietnam',
                'login_method' => 'Email',
                'team_value' => 98000000,
                'buyer_id' => null,
                'note' => 'UY TÍN 100%',
                'thumb' => 'https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg',
                'images' => json_encode([
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg"
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_category_id' => 1,
                'account_name' => 'player4',
                'password' => '123456',
                'price' => 50000,
                'status' => 'sold',
                'account_version' => 'global',
                'login_method' => 'Google',
                'team_value' => 65000000,
                'buyer_id' => 2,
                'note' => 'UY TÍN 100%',
                'thumb' => 'https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg',
                'images' => json_encode([
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg"
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'game_category_id' => 1,
                'account_name' => 'player5',
                'password' => '123456',
                'price' => 120000,
                'status' => 'available',
                'account_version' => 'vietnam',
                'login_method' => 'Garena',
                'team_value' => 110000000,
                'buyer_id' => null,
                'note' => 'UY TÍN 100%',
                'thumb' => 'https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg',
                'images' => json_encode([
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg",
                    "https://napfcpoint.com/upload/product/0f988d30b467f0a1d88b34ce5b6967ca.jpeg"
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
