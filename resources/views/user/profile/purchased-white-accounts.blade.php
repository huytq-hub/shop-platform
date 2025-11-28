@extends('layouts.user.app')
@section('title', $title)
@section('content')
    <div class="profile-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('layouts.user.sidebar')
                </div>
                <div class="col-lg-9">
                    <div class="profile-content">
                        <div class="profile-header">
                            <h2>{{ $title }}</h2>
                            <p>Danh sách acc trắng/reroll đã mua. Thông tin user/pass chỉ hiển thị tại đây.</p>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @forelse ($purchases as $purchase)
                            <div class="white-purchase-card">
                                <div class="white-purchase-card__header">
                                    <div>
                                        <strong>#{{ $purchase->id }}</strong>
                                        <span
                                            class="badge {{ $purchase->account_type === 'white' ? 'bg-success' : 'bg-info' }}">
                                            {{ $purchase->account_type === 'white' ? 'Acc trắng' : 'Acc reroll' }}
                                        </span>
                                    </div>
                                    <div class="text-muted">
                                        {{ $purchase->created_at?->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div class="white-purchase-card__meta">
                                    <div>
                                        <span>Số lượng</span>
                                        <strong>{{ $purchase->quantity }}</strong>
                                    </div>
                                    <div>
                                        <span>Tổng tiền</span>
                                        <strong>{{ number_format($purchase->total_amount) }}đ</strong>
                                    </div>
                                    <div>
                                        <span>Lô</span>
                                        <strong>{{ $purchase->batch?->name ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="white-purchase-card__actions">
                                    <button class="btn btn-outline-primary btn-sm copy-btn"
                                        data-target="#payload-{{ $purchase->id }}">Copy danh sách</button>
                                    <button class="btn btn-outline-secondary btn-sm download-btn"
                                        data-target="#payload-{{ $purchase->id }}"
                                        data-file="white-accounts-{{ $purchase->id }}.txt">Tải TXT</button>
                                </div>
                                <textarea readonly id="payload-{{ $purchase->id }}" class="white-purchase-card__payload" rows="4">@foreach ($purchase->delivery_payload as $item)
{{ $item['account_name'] ?? '' }}|{{ $item['password'] ?? '' }}|{{ $item['login_method'] ?? 'Garena' }}
@endforeach</textarea>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                Chưa có giao dịch acc trắng nào. Vào trang <a href="{{ route('white-accounts.index') }}">mua acc
                                    trắng</a> để bắt đầu.
                            </div>
                        @endforelse

                        <div class="mt-3">
                            {{ $purchases->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .white-purchase-card {
            background: #111327;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .white-purchase-card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .white-purchase-card__meta {
            display: flex;
            gap: 20px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .white-purchase-card__meta span {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            color: #8d96b8;
        }

        .white-purchase-card__payload {
            width: 100%;
            background: #0b0d1c;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            color: #fff;
            font-family: 'Fira Code', monospace;
            font-size: 13px;
            padding: 12px;
        }

        .white-purchase-card__actions {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
    </style>
@endpush

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
                    btn.innerText = 'Đã copy!';
                    setTimeout(() => (btn.innerText = 'Copy danh sách'), 1500);
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





