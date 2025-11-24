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
use App\Models\GameCategory;
use Illuminate\Http\Request;

class GameCategoryController extends Controller
{
    public function index(string $slug, Request $request)
    {
        $category = GameCategory::where("slug", $slug)->firstOrFail();

        // Get all accounts linked to this category
        $accounts = GameAccount::where('game_category_id', $category->id);
        if (!$request->filled('status')) {
            $accounts->where('status', 'available');
        }

        // Apply filters if any are set
        if ($request->hasAny(['code', 'price_range', 'status', 'version', 'login_method', 'team_value_min', 'team_value_max'])) {
            // Filter by code/ID
            if ($request->filled('code')) {
                $accounts->where('id', $request->code);
            }

            // Filter by price range
            if ($request->filled('price_range')) {
                $range = explode('-', $request->price_range);
                if (count($range) == 2) {
                    $accounts->whereBetween('price', $range);
                } else {
                    $accounts->where('price', '>=', $range[0]);
                }
            }

            // Filter by status
            if ($request->filled('status')) {
                $accounts->where('status', $request->status);
            }

            // Filter by account version
            if ($request->filled('version')) {
                $accounts->where('account_version', $request->version);
            }

            // Filter by login method
            if ($request->filled('login_method')) {
                $accounts->where('login_method', 'LIKE', '%' . $request->login_method . '%');
            }

            // Filter by team value range
            if ($request->filled('team_value_min')) {
                $accounts->where('team_value', '>=', $request->team_value_min);
            }

            if ($request->filled('team_value_max')) {
                $accounts->where('team_value', '<=', $request->team_value_max);
            }

        }
        $accounts = $accounts->orderBy('id', 'DESC')->get();
        return view('user.category.show', compact('category', 'accounts'));
    }

    public function showAll()
    {
        $title = 'Danh mục bán nick game';

        // Get all categories with additional statistics
        $categories = Category::where('active', 1)->get();

        foreach ($categories as $category) {
            // Total accounts in this category
            $category->allAccount = GameAccount::where('game_category_id', $category->id)->count();

            // Sold accounts in this category
            $category->soldCount = GameAccount::where('game_category_id', $category->id)
                ->where('status', 'sold')
                ->count();
        }

        return view('user.category.show-all', compact('categories', 'title'));
    }
}
