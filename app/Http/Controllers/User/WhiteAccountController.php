<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MoneyTransaction;
use App\Models\WhiteAccount;
use App\Models\WhiteAccountBatch;
use App\Models\WhiteAccountPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhiteAccountController extends Controller
{
    public function index()
    {
        $batches = WhiteAccountBatch::where('is_active', true)
            ->where('account_type', 'white')
            ->withCount([
                'accounts as available_accounts' => function ($query) {
                    $query->where('status', 'available');
                },
            ])
            ->orderBy('name')
            ->get();

        $batchIds = $batches->pluck('id');
        $minPrices = WhiteAccount::select('batch_id', DB::raw('MIN(COALESCE(unit_price, 0)) as min_price'))
            ->whereIn('batch_id', $batchIds)
            ->where('status', 'available')
            ->groupBy('batch_id')
            ->pluck('min_price', 'batch_id');

        $maxPerOrder = (int) (config_get('white_account.max_per_order', 20) ?? 20);
        $minPerOrder = (int) (config_get('white_account.min_per_order', 1) ?? 1);

        return view('user.white-accounts.index', [
            'batches' => $batches,
            'minPrices' => $minPrices,
            'maxPerOrder' => max($minPerOrder, $maxPerOrder),
            'minPerOrder' => $minPerOrder,
        ]);
    }

    public function purchase(Request $request, WhiteAccountBatch $batch)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!$batch->is_active) {
            return back()->with('error', 'Lô tài khoản này đang tạm khóa. Vui lòng chọn lô khác.');
        }

        $user = Auth::user();
        $quantity = (int) $request->quantity;
        $maxPerOrder = (int) (config_get('white_account.max_per_order', 20) ?? 20);
        $minPerOrder = (int) (config_get('white_account.min_per_order', 1) ?? 1);

        if ($quantity < $minPerOrder) {
            return back()->with('error', 'Số lượng tối thiểu mỗi đơn là ' . $minPerOrder . ' tài khoản.');
        }

        if ($quantity > $maxPerOrder) {
            return back()->with('error', 'Số lượng tối đa mỗi đơn là ' . $maxPerOrder . ' tài khoản.');
        }

        try {
            DB::beginTransaction();

            $accounts = WhiteAccount::where('batch_id', $batch->id)
                ->where('status', 'available')
                ->orderBy('id')
                ->lockForUpdate()
                ->limit($quantity)
                ->get();

            if ($accounts->count() < $quantity) {
                DB::rollBack();
                return back()->with('error', 'Kho không đủ số lượng. Vui lòng giảm số lượng hoặc chọn lô khác.');
            }

            $totalAmount = 0;
            $payload = [];
            foreach ($accounts as $account) {
                $price = $account->unit_price ?? $batch->default_price;
                $totalAmount += $price;
                $payload[] = [
                    'account_name' => $account->account_name,
                    'password' => $account->password,
                    'login_method' => $account->login_method,
                    'price' => $price,
                ];
            }

            if ($totalAmount <= 0) {
                DB::rollBack();
                return back()->with('error', 'Không thể xác định giá trị đơn hàng.');
            }

            if ($user->balance < $totalAmount) {
                DB::rollBack();
                return back()->with('error', 'Số dư không đủ. Vui lòng nạp thêm tiền để tiếp tục.');
            }

            $balanceBefore = $user->balance;
            $balanceAfter = $balanceBefore - $totalAmount;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['balance' => $balanceAfter]);

            $purchase = WhiteAccountPurchase::create([
                'user_id' => $user->id,
                'batch_id' => $batch->id,
                'account_type' => $batch->account_type,
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'delivery_payload' => $payload,
                'status' => 'completed',
            ]);

            $accountIds = $accounts->pluck('id');
            WhiteAccount::whereIn('id', $accountIds)->update([
                'status' => 'sold',
                'buyer_id' => $user->id,
                'purchase_id' => $purchase->id,
                'sold_at' => now(),
            ]);

            MoneyTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => -$totalAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Mua acc trắng (' . $batch->name . ') x' . $quantity,
                'reference_id' => 'WA-' . $purchase->id,
            ]);

            DB::commit();

            return redirect()->route('profile.purchased-accounts')
                ->with('success', 'Mua acc trắng thành công! Kiểm tra danh sách bàn giao trong mục "Tài khoản đã mua".');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Không thể hoàn tất giao dịch: ' . $th->getMessage());
        }
    }
}





