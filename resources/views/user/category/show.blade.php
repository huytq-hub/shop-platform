{{-- /**
* Copyright (c) 2025 FPT University
*
* @author Phạm Hoàng Tuấn
* @email phamhoangtuanqn@gmail.com
* @facebook fb.com/phamhoangtuanqn
*/ --}}

@extends('layouts.user.app')

@section('title', $category->name)

@section('content')
    <!-- Hero Section -->
    <x-hero-header title="{{ $category->name }}" description="{{ $category->description }}" />

    <!-- Account List Section -->
    <section class="account-section">
        <div class="container">
            <!-- Filter Bar -->
            <form action="" method="GET" class="account-filter">
                <div class="account-filter__row">
                    <div class="account-filter__group">
                        <label for="code" class="account-filter__label">Mã Số:</label>
                        <input type="text" id="code" name="code" class="account-filter__input"
                            placeholder="Nhập Mã Số" value="{{ request('code') }}">
                    </div>

                    <div class="account-filter__group">
                        <label for="price_range" class="account-filter__label">Giá:</label>
                        <select id="price_range" name="price_range"
                            class="account-filter__input account-filter__input--select">
                            <option value="">Tất cả</option>
                            <option value="0-50000" {{ request('price_range') == '0-50000' ? 'selected' : '' }}>Dưới 50K
                            </option>
                            <option value="50000-200000" {{ request('price_range') == '50000-200000' ? 'selected' : '' }}>
                                50K
                                - 200K</option>
                            <option value="200000-500000" {{ request('price_range') == '200000-500000' ? 'selected' : '' }}>
                                200K - 500K</option>
                            <option value="500000-1000000"
                                {{ request('price_range') == '500000-1000000' ? 'selected' : '' }}>
                                500K - 1M</option>
                            <option value="1000000" {{ request('price_range') == '1000000' ? 'selected' : '' }}>Trên 1M
                            </option>
                        </select>
                    </div>

                    <div class="account-filter__group">
                        <label for="status" class="account-filter__label">Trạng thái:</label>
                        <select id="status" name="status" class="account-filter__input account-filter__input--select">
                            <option value="">Trạng Thái</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Chưa bán
                            </option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Đã bán</option>
                        </select>
                    </div>
                </div>

                <div class="account-filter__row">
                    <div class="account-filter__group">
                        <label for="version" class="account-filter__label">Bản:</label>
                        <select id="version" name="version" class="account-filter__input account-filter__input--select">
                            <option value="">Tất cả</option>
                            <option value="vietnam" {{ request('version') == 'vietnam' ? 'selected' : '' }}>Bản Việt Nam
                            </option>
                            <option value="global" {{ request('version') == 'global' ? 'selected' : '' }}>Bản Quốc tế
                            </option>
                        </select>
                    </div>

                    <div class="account-filter__group">
                        <label for="login_method" class="account-filter__label">Hình thức login:</label>
                        <input type="text" id="login_method" name="login_method"
                            class="account-filter__input account-filter__input--select"
                            placeholder="Garena, Facebook, Email..."
                            value="{{ request('login_method') }}">
                    </div>

                    <div class="account-filter__group">
                        <label for="team_value_min" class="account-filter__label">Giá trị đội hình từ:</label>
                        <input type="number" id="team_value_min" name="team_value_min" min="0"
                            class="account-filter__input" placeholder="Ví dụ: 10000000"
                            value="{{ request('team_value_min') }}">
                    </div>

                    <div class="account-filter__group">
                        <label for="team_value_max" class="account-filter__label">Đến:</label>
                        <input type="number" id="team_value_max" name="team_value_max" min="0"
                            class="account-filter__input" placeholder="Ví dụ: 50000000"
                            value="{{ request('team_value_max') }}">
                    </div>
                </div>

                <div class="account-filter__actions">
                    <button type="submit" class="account-filter__button account-filter__button--primary">
                        <i class="fa-solid fa-filter"></i> TÌM KIẾM
                    </button>
                    <a href="{{ request()->url() }}" class="account-filter__button account-filter__button--outline">
                        <i class="fa-solid fa-rotate"></i> ĐẶT LẠI
                    </a>
                </div>
            </form>

            <!-- Account Grid -->
            <div class="account-grid">
                @forelse($accounts as $account)
                    <div class="account-card">
                        <div class="account-media">
                            <a href="{{ route('account.show', ['id' => $account->id]) }}">
                                <img src="{{ $account->thumb }}" alt="Account Preview" class="account-img">
                            </a>
                            <div class="account-code">Mã số: {{ $account->id }}</div>
                            <div class="account-price-top">ATM/VÍ ĐIỆN TỬ: {{ number_format($account->price) }} VND</div>
                        </div>

                        <div class="account-info">
                            <div class="account-row">
                                <div class="info-item">
                                    <span class="info-item__title">SEVER</span>
                                    <span class="info-value">{{ display_account_version($account->account_version) }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-item__title">Login:</span>
                                    <span class="info-value">
                                        {{ display_login_method($account->login_method) }}
                                    </span>
                                </div>
                            </div>
                            <div class="account-row">
                                <div class="info-item">
                                    <span class="info-item__title">Giá trị đội hình:</span>
                                    <span class="info-value">
                                        {{ display_team_value($account->team_value, 'Chưa cập nhật') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="account-actions">
                            <div class="card-price">CARD:
                                {{ number_format($account->price / ((100 - config_get('payment.card.discount_percent')) / 100)) }}
                                Đ</div>
                            <a href="{{ route('account.show', ['id' => $account->id]) }}"
                                class="action-btn action-btn--detail">XEM CHI TIẾT</a>
                        </div>
                    </div>
                @empty
                    <div class="no-data">
                        <p class="no-data-text">Không có tài khoản nào</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            {{-- {{ $accounts->links() }} --}}
        </div>
    </section>
@endsection
