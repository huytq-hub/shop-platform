<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhiteAccountBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WhiteAccountBatchController extends Controller
{
    public function index()
    {
        $title = 'Lô acc trắng Garena';
        $batches = WhiteAccountBatch::withCount([
            'accounts as total_accounts',
            'accounts as available_accounts' => function ($query) {
                $query->where('status', 'available');
            },
            'accounts as sold_accounts' => function ($query) {
                $query->where('status', 'sold');
            },
        ])->orderByDesc('id')->get();

        return view('admin.white-accounts.batches.index', compact('title', 'batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:white_account_batches,slug',
            'account_type' => 'required|in:white,reroll_white',
            'default_price' => 'required|numeric|min:0',
            'login_method' => 'required|string|max:100',
            'stock_warning_threshold' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'note' => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['stock_warning_threshold'] = $data['stock_warning_threshold'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        WhiteAccountBatch::create($data);

        return redirect()->route('admin.white-account-batches.index')
            ->with('success', 'Đã tạo lô acc trắng mới.');
    }

    public function edit(WhiteAccountBatch $batch)
    {
        $title = 'Chỉnh sửa lô acc trắng';
        return view('admin.white-accounts.batches.edit', compact('title', 'batch'));
    }

    public function update(Request $request, WhiteAccountBatch $batch)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:white_account_batches,slug,' . $batch->id,
            'account_type' => 'required|in:white,reroll_white',
            'default_price' => 'required|numeric|min:0',
            'login_method' => 'required|string|max:100',
            'stock_warning_threshold' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'note' => 'nullable|string',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['stock_warning_threshold'] = $data['stock_warning_threshold'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        $batch->update($data);

        return redirect()->route('admin.white-account-batches.index')
            ->with('success', 'Đã cập nhật lô acc trắng.');
    }

    public function destroy(WhiteAccountBatch $batch)
    {
        if ($batch->accounts()->exists()) {
            return redirect()->back()
                ->with('error', 'Không thể xóa lô khi vẫn còn tài khoản gắn với nó.');
        }

        $batch->delete();

        return redirect()->route('admin.white-account-batches.index')
            ->with('success', 'Đã xóa lô acc trắng.');
    }

    public function quickToggle(Request $request, WhiteAccountBatch $batch)
    {
        $batch->update(['is_active' => !$batch->is_active]);

        return redirect()->route('admin.white-account-batches.index')
            ->with('success', 'Đã cập nhật trạng thái hiển thị.');
    }
}





