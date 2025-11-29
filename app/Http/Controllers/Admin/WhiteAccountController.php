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
        $rules = [
            'batch_id' => 'required|exists:white_account_batches,id',
            'unit_price' => 'nullable|numeric|min:0',
            'import_method' => 'required|in:text,file',
        ];

        // Conditional validation based on import method
        if ($request->input('import_method') === 'file') {
            $rules['import_file'] = 'required|file|mimes:txt,csv|max:10240'; // Max 10MB
            $rules['accounts_data'] = 'nullable|string';
        } else {
            $rules['accounts_data'] = 'required|string';
            $rules['import_file'] = 'nullable|file|mimes:txt,csv|max:10240';
        }

        $data = $request->validate($rules);

        $batch = WhiteAccountBatch::findOrFail($data['batch_id']);

        // Get content from textarea or file
        $content = '';
        if ($data['import_method'] === 'file' && $request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $content = file_get_contents($file->getRealPath());
            // Handle BOM and encoding issues
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        } elseif ($data['import_method'] === 'text' && !empty($data['accounts_data'])) {
            $content = $data['accounts_data'];
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vui lòng nhập dữ liệu hoặc upload file.');
        }

        // Parse content
        $lines = preg_split('/\r\n|[\r\n]/', trim($content));
        $records = [];
        $now = now();
        $errors = [];
        $lineNumber = 0;

        foreach ($lines as $line) {
            $lineNumber++;
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Support both | and tab separator
            $parts = preg_split('/[\|\t]/', $line);
            $parts = array_map('trim', $parts);
            
            if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
                $errors[] = "Dòng $lineNumber: Định dạng không hợp lệ (thiếu username hoặc password)";
                continue;
            }

            [$accountName, $password, $customPrice] = array_pad($parts, 3, null);

            // Validate account name and password
            if (strlen($accountName) < 3) {
                $errors[] = "Dòng $lineNumber: Username quá ngắn (tối thiểu 3 ký tự)";
                continue;
            }

            if (strlen($password) < 3) {
                $errors[] = "Dòng $lineNumber: Password quá ngắn (tối thiểu 3 ký tự)";
                continue;
            }

            // Validate price if provided
            $finalPrice = $batch->default_price;
            if ($customPrice !== null && $customPrice !== '') {
                $price = (int) $customPrice;
                if ($price < 0) {
                    $errors[] = "Dòng $lineNumber: Giá không hợp lệ ($price)";
                    continue;
                }
                $finalPrice = $price;
            } elseif (isset($data['unit_price']) && $data['unit_price'] > 0) {
                $finalPrice = (int) $data['unit_price'];
            }

            $records[] = [
                'batch_id' => $batch->id,
                'account_name' => $accountName,
                'password' => $password,
                'login_method' => $batch->login_method,
                'unit_price' => $finalPrice,
                'status' => 'available',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($records)) {
            $errorMsg = 'Không có dòng hợp lệ nào để import.';
            if (!empty($errors)) {
                $errorMsg .= '<br><small>' . implode('<br>', array_slice($errors, 0, 5)) . '</small>';
                if (count($errors) > 5) {
                    $errorMsg .= '<br><small>... và ' . (count($errors) - 5) . ' lỗi khác</small>';
                }
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMsg);
        }

        try {
            DB::beginTransaction();
            DB::table('white_accounts')->insert($records);
            DB::commit();

            $successMsg = 'Đã import thành công ' . count($records) . ' tài khoản.';
            if (!empty($errors)) {
                $successMsg .= '<br><small class="text-warning">Có ' . count($errors) . ' dòng bị bỏ qua do lỗi.</small>';
            }

            return redirect()->route('admin.white-accounts.index')
                ->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lỗi khi import: ' . $e->getMessage());
        }
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





