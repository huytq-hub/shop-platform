<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhiteAccount;
use App\Models\WhiteAccountBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhiteAccountController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Kho acc trắng Garena';
        $batches = WhiteAccountBatch::orderBy('name')->get();

        $accountsQuery = WhiteAccount::with('batch')
            ->orderByDesc('id');

        if ($request->filled('batch_id')) {
            $accountsQuery->where('batch_id', $request->batch_id);
        }

        if ($request->filled('status')) {
            $accountsQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $accountsQuery->where(function ($query) use ($search) {
                $query->where('account_name', 'like', '%' . $search . '%')
                    ->orWhere('buyer_id', $search)
                    ->orWhere('id', $search);
            });
        }

        $accounts = $accountsQuery->paginate(25);

        $stats = [
            'available' => WhiteAccount::where('status', 'available')->count(),
            'reserved' => WhiteAccount::where('status', 'reserved')->count(),
            'sold' => WhiteAccount::where('status', 'sold')->count(),
        ];

        return view('admin.white-accounts.index', compact('title', 'accounts', 'batches', 'stats'));
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'batch_id' => 'required|exists:white_account_batches,id',
            'accounts_data' => 'required|string',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $batch = WhiteAccountBatch::findOrFail($data['batch_id']);

        $lines = preg_split('/\r\n|[\r\n]/', trim($data['accounts_data']));
        $records = [];
        $now = now();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                continue;
            }

            [$accountName, $password, $customPrice] = array_pad($parts, 3, null);

            $records[] = [
                'batch_id' => $batch->id,
                'account_name' => $accountName,
                'password' => $password,
                'login_method' => $batch->login_method,
                'unit_price' => $customPrice !== null && $customPrice !== ''
                    ? (int) $customPrice
                    : ($data['unit_price'] ?? $batch->default_price),
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($records)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không có dòng hợp lệ nào để import.');
        }

        DB::table('white_accounts')->insert($records);

        return redirect()->route('admin.white-accounts.index')
            ->with('success', 'Đã import ' . count($records) . ' tài khoản trắng.');
    }

    public function destroy(WhiteAccount $whiteAccount)
    {
        if ($whiteAccount->status !== 'available') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể xóa tài khoản chưa bán.');
        }

        $whiteAccount->delete();

        return redirect()->route('admin.white-accounts.index')
            ->with('success', 'Đã xóa tài khoản trắng.');
    }
}





