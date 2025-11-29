{{-- /**
* Copyright (c) 2025 FPT University
*
* @author Phạm Hoàng Tuấn
* @email phamhoangtuanqn@gmail.com
* @facebook fb.com/phamhoangtuanqn
*/ --}}

@extends('layouts.user.app')
@section('title', $title)
@section('content')
    <x-hero-header title="DANH MỤC GAME" description="Danh sách các danh mục tài khoản game" />

    <section class="menu">
        <div class="container">
            <div class="category__list">
                @if ($categories->count() > 0)
                    @foreach ($categories as $category)
                        @php
                            $isWhiteCategory = ($category->type ?? 'standard') === 'white_accounts';
                            $isRerollCategory = ($category->type ?? 'standard') === 'reroll_accounts';
                            $categoryLink = $isWhiteCategory
                                ? route('white-accounts.index')
                                : ($isRerollCategory
                                    ? route('reroll-accounts.index')
                                    : route('category.index', ['slug' => $category->slug]));
                        @endphp
                        @if ($category->active)
                            <a href="{{ $categoryLink }}" class="category__item {{ $isWhiteCategory ? 'category__item--white' : '' }} {{ $isRerollCategory ? 'category__item--reroll' : '' }}">
                                <!-- @if ($isWhiteCategory)
                                    <div class="category__white-ribbon">ACC TRẮNG</div>
                                @endif -->
                                <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" class="category__img" />
                                <h2 class="category__title">{{ strtoupper($category->name) }}</h2>
                                <p class="category__desc">
                                    @if ($isWhiteCategory)
                                        Còn {{ number_format($category->allAccount) }} nick trắng, đã bán {{ number_format($category->soldCount) }}.
                                    @elseif ($isRerollCategory)
                                        Còn {{ number_format($category->allAccount) }} nick reroll, đã bán {{ number_format($category->soldCount) }}.
                                    @else
                                        Tổng tài khoản: {{ number_format($category->allAccount) }} | Đã bán: {{ number_format($category->soldCount) }}
                                    @endif
                                </p>
                                <p class="text category__action">
                                    {{ $isWhiteCategory ? 'Mua acc trắng' : ($isRerollCategory ? 'Mua acc reroll' : 'Mua ngay') }}
                                </p>
                            </a>
                        @endif
                    @endforeach
                @else
                    <div class="no-results">
                        <div class="no-results__content">
                            <i class="fas fa-exclamation-circle no-results__icon"></i>
                            <h2 class="no-results__title">Không tìm thấy danh mục!</h2>
                            <p class="no-results__message">Hiện tại không có danh mục game nào.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
