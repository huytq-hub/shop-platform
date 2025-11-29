@extends('layouts.user.app')

@section('title', $title)

@section('content')
    <section class="profile-section">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    <h1 class="profile-title"><i class="fa-solid fa-box me-2"></i> TÀI KHOẢN ĐÃ MUA</h1>
                </div>

                <div class="profile-content">
                    @include('layouts.user.sidebar')

                    <div class="profile-main">
                        <div class="profile-info-card">
                            <div class="info-header">
                                <div class="balance-info">
                                    <span class="balance-label"><i class="fa-solid fa-wallet me-2"></i> SỐ DƯ HIỆN TẠI:
                                        {{ number_format($user->balance) }} VND</span>
                                </div>
                            </div>

                            <div class="info-content">
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                    </div>
                                @endif

                                {{-- Game Accounts Section --}}
                                @if($transactions->count() > 0)
                                    <h3 class="mb-3" style="color: #fff; font-size: 18px; font-weight: 600;">
                                        <i class="fa-solid fa-gamepad me-2"></i> Tài khoản game
                                    </h3>
                                    <div class="transaction-history">
                                        <div class="history-table-container">
                                            <table class="history-table">
                                                <thead>
                                                    <tr>
                                                        <th>Thời gian</th>
                                                        <th>Server</th>
                                                        <th>Username</th>
                                                        <th>Password</th>
                                                        <th>Số tiền</th>
                                                        <th>Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->created_at->format('H:i d/m/Y') }}</td>
                                                            <td>Server {{ $transaction->server }}</td>
                                                            <td class="text-bold">{{ $transaction->account_name }}</td>
                                                            <td class="text-bold">{{ $transaction->password }}</td>
                                                            <td class="amount text-danger">
                                                                -{{ number_format($transaction->price) }} VND</td>
                                                            <td>
                                                                <a href="{{ route('account.show', ['id' => $transaction->id]) }}"
                                                                    class="btn btn-sm btn-info" target="_blank">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="pagination">
                                            {{ $transactions->links() }}
                                        </div>
                                    </div>
                                @endif

                                {{-- White Accounts Section --}}
                                @if(isset($whitePurchases) && $whitePurchases->count() > 0)
                                    @if($transactions->count() > 0)
                                        <hr style="border-color: rgba(255,255,255,0.1); margin: 30px 0;">
                                    @endif
                                    <h3 class="mb-3" style="color: #fff; font-size: 18px; font-weight: 600;">
                                        <i class="fa-solid fa-user me-2"></i> Acc trắng/Reroll đã mua
                                    </h3>
                                    <div class="white-accounts-section">
                                        @foreach($whitePurchases as $purchase)
                                            <div class="white-purchase-card" style="background: #111327; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                                                <div class="white-purchase-card__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                                    <div>
                                                        <strong style="color: #fff;">#{{ $purchase->id }}</strong>
                                                        <span class="badge {{ $purchase->account_type === 'white' ? 'bg-success' : 'bg-info' }}" style="margin-left: 8px;">
                                                            {{ $purchase->account_type === 'white' ? 'Acc trắng' : 'Acc reroll' }}
                                                        </span>
                                                    </div>
                                                    <div style="color: #8d96b8; font-size: 13px;">
                                                        {{ $purchase->created_at?->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                                <div class="white-purchase-card__meta" style="display: flex; gap: 20px; margin-bottom: 12px; flex-wrap: wrap;">
                                                    <div>
                                                        <span style="display: block; font-size: 12px; text-transform: uppercase; color: #8d96b8;">Số lượng</span>
                                                        <strong style="color: #fff;">{{ $purchase->quantity }}</strong>
                                                    </div>
                                                    <div>
                                                        <span style="display: block; font-size: 12px; text-transform: uppercase; color: #8d96b8;">Tổng tiền</span>
                                                        <strong style="color: #fff;">{{ number_format($purchase->total_amount) }}đ</strong>
                                                    </div>
                                                    <div>
                                                        <span style="display: block; font-size: 12px; text-transform: uppercase; color: #8d96b8;">Lô</span>
                                                        <strong style="color: #fff;">{{ $purchase->batch?->name ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                                <div class="white-purchase-card__actions" style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                                                    <button class="btn btn-outline-primary btn-sm copy-btn" data-target="#payload-{{ $purchase->id }}" style="font-size: 12px;">
                                                        <i class="fa-solid fa-copy me-1"></i> Copy danh sách
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm download-btn" data-target="#payload-{{ $purchase->id }}" data-file="white-accounts-{{ $purchase->id }}.txt" style="font-size: 12px;">
                                                        <i class="fa-solid fa-download me-1"></i> Tải TXT
                                                    </button>
                                                </div>
                                                <textarea readonly id="payload-{{ $purchase->id }}" class="white-purchase-card__payload" rows="4" style="width: 100%; background: #0b0d1c; border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 10px; color: #fff; font-family: 'Fira Code', monospace; font-size: 13px; padding: 12px;">@foreach ($purchase->delivery_payload as $item)
{{ $item['account_name'] ?? '' }}|{{ $item['password'] ?? '' }}|{{ $item['login_method'] ?? 'Garena' }}
@endforeach</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Empty State --}}
                                @if($transactions->count() === 0 && (!isset($whitePurchases) || $whitePurchases->count() === 0))
                                    <div class="alert alert-info" style="text-align: center; padding: 30px;">
                                        <i class="fa-solid fa-info-circle me-2"></i> Chưa có tài khoản nào được mua.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.copy-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = document.querySelector(btn.dataset.target);
                    if (!target) return;
                    target.select();
                    target.setSelectionRange(0, target.value.length);
                    document.execCommand('copy');
                    btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Đã copy!';
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fa-solid fa-copy me-1"></i> Copy danh sách';
                    }, 1500);
                });
            });

            document.querySelectorAll('.download-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = document.querySelector(btn.dataset.target);
                    if (!target) return;
                    const blob = new Blob([target.value], {
                        type: 'text/plain;charset=utf-8'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = btn.dataset.file;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            });
        });
    </script>
@endpush
