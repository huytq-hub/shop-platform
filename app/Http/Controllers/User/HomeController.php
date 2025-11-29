<?php
/**
 * Copyright (c) 2025 FPT University
 *
 * @author    Phạm Hoàng Tuấn
 * @email     phamhoangtuanqn@gmail.com
 * @facebook  fb.com/phamhoangtuanqn
 */

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\GameAccount;
use App\Models\GameService;
use App\Models\LuckyWheel;
use App\Models\ServiceHistory;
use App\Models\RandomCategory;
use App\Models\RandomCategoryAccount;
use App\Models\MoneyTransaction;
use App\Models\Notification;
use App\Models\WhiteAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //
    public function index()
    {
        $categories = Category::where('active', 1)->orderBy('updated_at', 'desc')->get();

        $whiteStats = [
            'available' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'available')
                ->where('wab.account_type', 'white')
                ->count(),
            'sold' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'sold')
                ->where('wab.account_type', 'white')
                ->count(),
            'min_price' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'available')
                ->where('wab.account_type', 'white')
                ->selectRaw('MIN(COALESCE(white_accounts.unit_price, wab.default_price)) as price')
                ->value('price'),
        ];

        $rerollStats = [
            'available' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'available')
                ->where('wab.account_type', 'reroll_white')
                ->count(),
            'sold' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'sold')
                ->where('wab.account_type', 'reroll_white')
                ->count(),
            'min_price' => WhiteAccount::join('white_account_batches as wab', 'white_accounts.batch_id', '=', 'wab.id')
                ->where('white_accounts.status', 'available')
                ->where('wab.account_type', 'reroll_white')
                ->selectRaw('MIN(COALESCE(white_accounts.unit_price, wab.default_price)) as price')
                ->value('price'),
        ];

        foreach ($categories as $category) {
            if ($category->type === 'white_accounts') {
                $category->allAccount = $whiteStats['available'];
                $category->soldCount = $whiteStats['sold'];
                $category->white_min_price = $whiteStats['min_price'];
            } elseif ($category->type === 'reroll_accounts') {
                $category->allAccount = $rerollStats['available'];
                $category->soldCount = $rerollStats['sold'];
                $category->white_min_price = $rerollStats['min_price'];
            } else {
                $category->soldCount = GameAccount::where('game_category_id', $category->id)
                    ->where('status', 'sold')
                    ->count();
                $category->allAccount = GameAccount::where('game_category_id', $category->id)->count();
            }
        }

        // Dịch vụ cày thuê
        $services = GameService::where('active', '1')->orderBy('updated_at', 'desc')->get();
        foreach ($services as $service) {
            $service->orderCount = ServiceHistory::where('game_service_id', $service->id)->count();
        }

        // Random categories
        $randomCategories = RandomCategory::where('active', 1)->orderBy('updated_at', 'desc')->get();
        foreach ($randomCategories as $category) {
            $category->soldCount = RandomCategoryAccount::where('random_category_id', $category->id)
                ->where('status', 'sold')
                ->count();
            $category->allAccount = RandomCategoryAccount::where('random_category_id', $category->id)->count();
        }

        // Vòng quay may mắn
        $LuckWheel = LuckyWheel::where('active', 1)->orderBy('updated_at', 'desc')->get();
        foreach ($LuckWheel as $wheel) {
            $wheel->soldCount = $wheel->histories->count();
        }

        // Lấy 20 giao dịch gần đây
        $recentTransactions = MoneyTransaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Lấy top 3 người nạp tiền nhiều nhất trong tháng hiện tại
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $topDepositors = MoneyTransaction::select('user_id', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'deposit')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('user_id')
            ->orderBy('total_amount', 'desc')
            ->limit(3)
            ->get();

        // Lấy thông tin người dùng cho top depositors
        foreach ($topDepositors as $depositor) {
            $depositor->user = \App\Models\User::find($depositor->user_id);
        }

        $notifications = Notification::orderBy('created_at', 'desc')->get();

        return view('user.home', compact(
            'categories',
            'services',
            'randomCategories',
            'LuckWheel',
            'recentTransactions',
            'topDepositors',
            'notifications'
        ));
    }
}
