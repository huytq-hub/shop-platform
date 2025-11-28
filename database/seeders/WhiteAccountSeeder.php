<?php

namespace Database\Seeders;

use App\Models\WhiteAccount;
use App\Models\WhiteAccountBatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WhiteAccountSeeder extends Seeder
{
    /**
     * Seed demo batches & accounts for white-account module.
     */
    public function run(): void
    {
        // Chỉ seed nếu chưa có batch nào
        if (WhiteAccountBatch::count() > 0) {
            $this->command->info('WhiteAccountSeeder: Đã có dữ liệu batch, bỏ qua seeding.');
            return;
        }

        try {
            DB::beginTransaction();

            $batches = [
                [
                    'name' => 'Garena trắng cơ bản',
                    'slug' => 'garena-white-basic',
                    'account_type' => 'white',
                    'default_price' => 10000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 10,
                    'is_active' => true,
                    'note' => 'Nick mới tạo, chưa đăng nhập lần nào. Dành cho khách cần user/pass trắng.',
                ],
                [
                    'name' => 'Garena reroll vượt tân thủ',
                    'slug' => 'garena-reroll-starter',
                    'account_type' => 'reroll_white',
                    'default_price' => 15000,
                    'login_method' => 'Garena',
                    'stock_warning_threshold' => 10,
                    'is_active' => true,
                    'note' => 'Nick đã hoàn thành nhiệm vụ tân thủ, sẵn sàng reroll cầu thủ.',
                ],
            ];

            $createdBatches = [];
            foreach ($batches as $batchData) {
                $slug = $batchData['slug'] ?? Str::slug($batchData['name']);
                
                // Kiểm tra xem slug đã tồn tại chưa
                $existing = WhiteAccountBatch::where('slug', $slug)->first();
                if ($existing) {
                    $createdBatches[$batchData['account_type']] = $existing;
                    $this->command->info("WhiteAccountSeeder: Batch '{$slug}' đã tồn tại, bỏ qua.");
                    continue;
                }

                $batch = WhiteAccountBatch::create($batchData);
                $createdBatches[$batchData['account_type']] = $batch;
                $this->command->info("WhiteAccountSeeder: Đã tạo batch '{$batch->name}' (ID: {$batch->id})");
            }

            $whiteAccounts = [
                [
                    'batch_type' => 'white',
                    'account_name' => 'garena_white_01@gmail.com',
                    'password' => 'PassWhite#01',
                    'unit_price' => 10000,
                ],
                [
                    'batch_type' => 'white',
                    'account_name' => 'garena_white_02@gmail.com',
                    'password' => 'PassWhite#02',
                    'unit_price' => 10000,
                ],
                [
                    'batch_type' => 'white',
                    'account_name' => 'garena_white_03@gmail.com',
                    'password' => 'PassWhite#03',
                    'unit_price' => 10000,
                ],
                [
                    'batch_type' => 'reroll_white',
                    'account_name' => 'garena_reroll_01@gmail.com',
                    'password' => 'PassReroll#01',
                    'unit_price' => 15000,
                ],
                [
                    'batch_type' => 'reroll_white',
                    'account_name' => 'garena_reroll_02@gmail.com',
                    'password' => 'PassReroll#02',
                    'unit_price' => 15000,
                ],
            ];

            $createdCount = 0;
            foreach ($whiteAccounts as $account) {
                $batch = $createdBatches[$account['batch_type']] ?? null;
                if (!$batch) {
                    $this->command->warn("WhiteAccountSeeder: Không tìm thấy batch cho type '{$account['batch_type']}', bỏ qua account '{$account['account_name']}'");
                    continue;
                }

                // Kiểm tra xem account đã tồn tại chưa
                $existing = WhiteAccount::where('batch_id', $batch->id)
                    ->where('account_name', $account['account_name'])
                    ->first();
                
                if ($existing) {
                    $this->command->info("WhiteAccountSeeder: Account '{$account['account_name']}' đã tồn tại, bỏ qua.");
                    continue;
                }

                WhiteAccount::create([
                    'batch_id' => $batch->id,
                    'account_name' => $account['account_name'],
                    'password' => $account['password'],
                    'login_method' => $batch->login_method,
                    'unit_price' => $account['unit_price'],
                    'status' => 'available',
                ]);
                $createdCount++;
            }

            DB::commit();
            $this->command->info("WhiteAccountSeeder: Hoàn thành! Đã tạo {$createdCount} tài khoản trắng.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("WhiteAccountSeeder: Lỗi - " . $e->getMessage());
            throw $e;
        }
    }
}

