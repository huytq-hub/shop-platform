<?php

namespace Database\Seeders;

use App\Models\WhiteAccount;
use App\Models\WhiteAccountBatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class WhiteAccountSeeder extends Seeder
{
    /**
     * Seed demo batches & accounts for white-account module.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $faker = Faker::create('vi_VN');

            // Tạo batches
            $batches = [
                [
                    'name' => 'Garena trắng tháng 12',
                    'slug' => 'garena-white-dec',
                    'account_type' => 'white',
                    'default_price' => 10000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 20,
                    'is_active' => true,
                    'note' => 'Nick mới tạo, chưa đăng nhập lần nào. Dành cho khách cần user/pass trắng.',
                ],
                [
                    'name' => 'Garena trắng premium',
                    'slug' => 'garena-white-premium',
                    'account_type' => 'white',
                    'default_price' => 15000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 15,
                    'is_active' => true,
                    'note' => 'Nick trắng chất lượng cao, đảm bảo chưa sử dụng.',
                ],
                [
                    'name' => 'Garena reroll vượt tân thủ',
                    'slug' => 'garena-reroll-starter',
                    'account_type' => 'reroll_white',
                    'default_price' => 20000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 20,
                    'is_active' => true,
                    'note' => 'Nick đã hoàn thành nhiệm vụ tân thủ, sẵn sàng reroll cầu thủ.',
                ],
                [
                    'name' => 'Garena reroll tháng 12',
                    'slug' => 'garena-reroll-dec',
                    'account_type' => 'reroll_white',
                    'default_price' => 18000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 15,
                    'is_active' => true,
                    'note' => 'Reroll đã qua tân thủ, có thể reroll ngay.',
                ],
            ];

            $createdBatches = [];
            foreach ($batches as $batchData) {
                $slug = $batchData['slug'] ?? Str::slug($batchData['name']);
                
                // Kiểm tra xem slug đã tồn tại chưa
                $existing = WhiteAccountBatch::where('slug', $slug)->first();
                if ($existing) {
                    $createdBatches[$slug] = $existing;
                    $this->command->info("WhiteAccountSeeder: Batch '{$slug}' đã tồn tại, sử dụng batch hiện có.");
                    continue;
                }

                $batch = WhiteAccountBatch::create($batchData);
                $createdBatches[$slug] = $batch;
                $this->command->info("WhiteAccountSeeder: Đã tạo batch '{$batch->name}' (ID: {$batch->id})");
            }

            // Tạo accounts cho mỗi batch
            $accountsToCreate = [
                'garena-white-dec' => 25,        // 25 white accounts
                'garena-white-premium' => 15,     // 15 white accounts
                'garena-reroll-starter' => 30,    // 30 reroll accounts
                'garena-reroll-dec' => 20,        // 20 reroll accounts
            ];

            $totalCreated = 0;
            $now = now();

            foreach ($createdBatches as $slug => $batch) {
                $count = $accountsToCreate[$slug] ?? 0;
                if ($count <= 0) {
                    continue;
                }

                $records = [];
                $basePrice = $batch->default_price;

                for ($i = 1; $i <= $count; $i++) {
                    // Tạo username ngẫu nhiên nhưng có format
                    $accountName = $batch->account_type === 'white' 
                        ? 'garena_white_' . str_pad($i, 3, '0', STR_PAD_LEFT) . '@gmail.com'
                        : 'garena_reroll_' . str_pad($i, 3, '0', STR_PAD_LEFT) . '@gmail.com';
                    
                    // Tạo password ngẫu nhiên nhưng có format
                    $password = $batch->account_type === 'white'
                        ? 'PassWhite#' . str_pad($i, 3, '0', STR_PAD_LEFT)
                        : 'PassReroll#' . str_pad($i, 3, '0', STR_PAD_LEFT);

                    // Một số accounts có giá riêng (variation)
                    $unitPrice = $basePrice;
                    if ($i % 5 === 0) {
                        // Mỗi 5 accounts có giá khác một chút
                        $unitPrice = $basePrice + ($faker->numberBetween(1000, 5000));
                    }

                    // Kiểm tra xem account đã tồn tại chưa
                    $existing = WhiteAccount::where('batch_id', $batch->id)
                        ->where('account_name', $accountName)
                        ->first();
                    
                    if ($existing) {
                        continue;
                    }

                    $records[] = [
                        'batch_id' => $batch->id,
                        'account_name' => $accountName,
                        'password' => $password,
                        'login_method' => $batch->login_method,
                        'unit_price' => $unitPrice,
                        'status' => 'available',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($records)) {
                    // Insert batch để tối ưu performance
                    DB::table('white_accounts')->insert($records);
                    $totalCreated += count($records);
                    $this->command->info("WhiteAccountSeeder: Đã tạo " . count($records) . " accounts cho batch '{$batch->name}'");
                }
            }

            DB::commit();
            $this->command->info("WhiteAccountSeeder: Hoàn thành! Đã tạo tổng cộng {$totalCreated} tài khoản.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("WhiteAccountSeeder: Lỗi - " . $e->getMessage());
            throw $e;
        }
    }
}

