@extends('layouts.user.app')
@section('title', 'Acc trắng Garena')
@section('content')
    <x-hero-header title="ACC TRẮNG GARENA" description="Mua nhanh tài khoản trắng (chỉ user/pass), chưa đăng nhập lần nào" />

    <div class="container white-accounts-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show white-alert" role="alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show white-alert" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="white-accounts-info">
            <div class="info-card">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Giao hàng ngay:</strong> Sau khi thanh toán, tài khoản sẽ hiển thị trong mục 
                    <a href="{{ route('profile.white-accounts') }}">Acc trắng đã mua</a>
                </div>
            </div>
            <div class="info-card">
                <i class="fas fa-shopping-cart"></i>
                <div>
                    <strong>Giới hạn đơn hàng:</strong> Tối thiểu {{ $minPerOrder }} / Tối đa {{ $maxPerOrder }} tài khoản mỗi đơn
                </div>
            </div>
            <div class="info-card">
                <i class="fas fa-key"></i>
                <div>
                    <strong>Login mặc định:</strong> Garena (trừ khi lô có ghi chú khác)
                </div>
            </div>
        </div>

        @if ($batches->count() > 0)
            <div class="white-batch-grid" id="batchGrid">
                @foreach ($batches as $batch)
                    <div class="white-batch-card white-batch-card--white">
                        <div class="white-batch-card__badge">
                            <i class="fas fa-user"></i>
                            ACC TRẮNG
                        </div>
                        
                        <div class="white-batch-card__header">
                            <div class="white-batch-card__login-badge">
                                <i class="fas fa-key"></i> {{ $batch->login_method }}
                            </div>
                        </div>

                        <h3 class="white-batch-card__title">{{ $batch->name }}</h3>
                        
                        @if ($batch->note)
                            <p class="white-batch-card__note">
                                <i class="fas fa-sticky-note"></i> {{ $batch->note }}
                            </p>
                        @endif

                        <div class="white-batch-card__stats">
                            <div class="stat-item">
                                <div class="stat-label">
                                    <i class="fas fa-tag"></i> Giá từ
                                </div>
                                <div class="stat-value price">
                                    {{ number_format($minPrices[$batch->id] ?? $batch->default_price) }}₫
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">
                                    <i class="fas fa-box"></i> Tồn kho
                                </div>
                                <div class="stat-value {{ $batch->available_accounts === 0 ? 'out-of-stock' : 'in-stock' }}">
                                    {{ number_format($batch->available_accounts) }} nick
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('white-accounts.purchase', $batch) }}" method="POST"
                            class="white-batch-card__form">
                            @csrf
                            <div class="form-group">
                                <label for="quantity-{{ $batch->id }}">
                                    <i class="fas fa-shopping-cart"></i> Số lượng muốn mua
                                </label>
                                <div class="quantity-input-wrapper">
                                    <button type="button" class="qty-btn qty-minus" data-target="quantity-{{ $batch->id }}">-</button>
                                    <input type="number" id="quantity-{{ $batch->id }}" name="quantity"
                                        min="{{ $minPerOrder }}" max="{{ min($maxPerOrder, $batch->available_accounts) }}"
                                        value="{{ old('quantity', $minPerOrder) }}" 
                                        class="form-control quantity-input" 
                                        required
                                        {{ $batch->available_accounts === 0 ? 'disabled' : '' }}>
                                    <button type="button" class="qty-btn qty-plus" data-target="quantity-{{ $batch->id }}">+</button>
                                </div>
                                <small class="form-text">
                                    Tối đa: {{ min($maxPerOrder, $batch->available_accounts) }} nick
                                </small>
                            </div>
                            
                            @auth
                                <button type="submit" class="btn-purchase {{ $batch->available_accounts === 0 ? 'btn-out-of-stock' : '' }}"
                                    {{ $batch->available_accounts === 0 ? 'disabled' : '' }}>
                                    @if ($batch->available_accounts === 0)
                                        <i class="fas fa-times-circle"></i> HẾT HÀNG
                                    @else
                                        <i class="fas fa-shopping-bag"></i> MUA NGAY
                                    @endif
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn-purchase btn-login">
                                    <i class="fas fa-sign-in-alt"></i> ĐĂNG NHẬP ĐỂ MUA
                                </a>
                            @endauth
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>Chưa có lô acc trắng nào đang bán</h3>
                <p>Vui lòng quay lại sau hoặc liên hệ admin để được hỗ trợ.</p>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <style>
        .white-accounts-wrapper {
            padding: 30px 15px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .white-alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .white-alert i {
            font-size: 20px;
            flex-shrink: 0;
        }

        .white-alert span {
            flex: 1;
        }

        .white-alert .btn-close {
            opacity: 0.8;
            padding: 0;
            margin: 0;
        }

        .white-alert .btn-close:hover {
            opacity: 1;
        }

        .white-accounts-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .info-card {
            background: linear-gradient(135deg, rgba(0, 188, 212, 0.08) 0%, rgba(79, 195, 247, 0.08) 100%);
            border: 1px solid rgba(0, 188, 212, 0.25);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #00bcd4 0%, #4fc3f7 100%);
        }

        .info-card:hover {
            transform: translateY(-2px);
            border-color: rgba(0, 188, 212, 0.4);
            box-shadow: 0 4px 16px rgba(0, 188, 212, 0.15);
        }

        .info-card i {
            color: #00bcd4;
            font-size: 24px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .info-card strong {
            color: #fff;
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .info-card div {
            color: #b8c5d6;
            font-size: 14px;
            line-height: 1.6;
        }

        .info-card a {
            color: #00bcd4;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .info-card a:hover {
            color: #4fc3f7;
            text-decoration: underline;
        }

        .white-batch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 28px;
        }

        .white-batch-card {
            background: linear-gradient(135deg, rgba(15, 17, 31, 0.98) 0%, rgba(20, 22, 39, 0.98) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 28px;
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .white-batch-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #00bcd4 0%, #4fc3f7 100%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .white-batch-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 188, 212, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .white-batch-card:hover {
            transform: translateY(-6px);
            border-color: rgba(0, 188, 212, 0.4);
            box-shadow: 0 12px 32px rgba(0, 188, 212, 0.25);
        }

        .white-batch-card:hover::before {
            opacity: 1;
        }

        .white-batch-card:hover::after {
            opacity: 1;
        }

        .white-batch-card--white::before {
            background: linear-gradient(90deg, #ffd54f 0%, #ffb74d 100%);
        }

        .white-batch-card--white:hover {
            border-color: rgba(255, 213, 79, 0.4);
            box-shadow: 0 12px 32px rgba(255, 213, 79, 0.25);
        }


        .white-batch-card__badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, rgba(0, 188, 212, 0.2) 0%, rgba(79, 195, 247, 0.2) 100%);
            border: 1px solid rgba(0, 188, 212, 0.4);
            color: #00bcd4;
            padding: 8px 14px;
            border-radius: 24px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0, 188, 212, 0.2);
            z-index: 2;
        }

        .white-batch-card--white .white-batch-card__badge {
            background: linear-gradient(135deg, rgba(255, 213, 79, 0.2) 0%, rgba(255, 183, 77, 0.2) 100%);
            border-color: rgba(255, 213, 79, 0.4);
            color: #ffd54f;
            box-shadow: 0 2px 8px rgba(255, 213, 79, 0.2);
        }

        .white-batch-card__header {
            margin-bottom: 12px;
        }

        .white-batch-card__login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 188, 212, 0.1);
            border: 1px solid rgba(0, 188, 212, 0.2);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: #00bcd4;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .white-batch-card__login-badge i {
            color: #00bcd4;
            font-size: 13px;
        }

        .white-batch-card__title {
            margin: 0 0 16px;
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            padding-right: 100px;
        }

        .white-batch-card__note {
            color: #b8c5d6;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px;
            background: rgba(255, 213, 79, 0.05);
            border-left: 3px solid rgba(255, 213, 79, 0.3);
            border-radius: 6px;
        }

        .white-batch-card__note i {
            color: #ffd54f;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .white-batch-card__stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin: 24px 0;
            padding: 18px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 11px;
            color: #9ea7c2;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 600;
        }

        .stat-label i {
            font-size: 11px;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .stat-value.price {
            color: #4caf50;
            text-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        .stat-value.in-stock {
            color: #4caf50;
            text-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        .stat-value.out-of-stock {
            color: #f44336;
            text-shadow: 0 0 10px rgba(244, 67, 54, 0.3);
        }

        .white-batch-card__form {
            margin-top: auto;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #9ea7c2;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group label i {
            color: #00bcd4;
        }

        .quantity-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(11, 13, 24, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 4px;
        }

        .qty-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .qty-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, rgba(0, 188, 212, 0.3) 0%, rgba(79, 195, 247, 0.3) 100%);
            border-color: #00bcd4;
            transform: scale(1.05);
        }

        .qty-btn:active:not(:disabled) {
            transform: scale(0.95);
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .quantity-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            padding: 8px;
        }

        .quantity-input:focus {
            outline: none;
        }

        .quantity-input:disabled {
            opacity: 0.5;
        }

        .form-text {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            color: #9ea7c2;
        }

        .btn-purchase {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-purchase::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-purchase:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-purchase:not(.btn-out-of-stock):not(.btn-login) {
            background: linear-gradient(135deg, #00bcd4 0%, #4fc3f7 100%);
            color: #fff;
            box-shadow: 0 4px 16px rgba(0, 188, 212, 0.3);
        }

        .btn-purchase:not(.btn-out-of-stock):not(.btn-login):hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 188, 212, 0.5);
        }

        .btn-purchase:not(.btn-out-of-stock):not(.btn-login):active {
            transform: translateY(-1px);
        }

        .btn-out-of-stock {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.15) 0%, rgba(229, 57, 53, 0.15) 100%);
            border: 1px solid rgba(244, 67, 54, 0.4);
            color: #f44336;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .btn-login:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(0, 188, 212, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #9ea7c2;
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 18px;
        }

        .empty-state i {
            font-size: 72px;
            color: rgba(255, 255, 255, 0.08);
            margin-bottom: 24px;
            display: block;
        }

        .empty-state h3 {
            color: #fff;
            margin-bottom: 12px;
            font-size: 22px;
        }

        .empty-state p {
            font-size: 15px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .white-accounts-wrapper {
                padding: 20px 10px 40px;
            }

            .white-batch-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .white-accounts-info {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .white-batch-card {
                padding: 20px;
            }

            .white-batch-card__title {
                font-size: 20px;
                padding-right: 80px;
            }

            .white-batch-card__stats {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity input controls
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    if (!input || input.disabled) return;

                    const current = parseInt(input.value) || 0;
                    const min = parseInt(input.min) || 1;
                    const max = parseInt(input.max) || 20;
                    const isPlus = this.classList.contains('qty-plus');

                    if (isPlus && current < max) {
                        input.value = current + 1;
                    } else if (!isPlus && current > min) {
                        input.value = current - 1;
                    }
                });
            });
        });
    </script>
@endpush





