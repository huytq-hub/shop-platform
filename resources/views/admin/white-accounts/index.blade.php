@extends('layouts.admin.app')
@section('title', $title)
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>{{ $title }}</h4>
                    <h6>Nhập kho và quản lý acc trắng dạng user/pass</h6>
                </div>
                <div class="page-btn">
                    <a href="{{ route('admin.white-account-batches.index') }}" class="btn btn-added">
                        <i class="fas fa-box"></i> Quản lý lô
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Tình trạng kho</h5>
                            <div class="d-flex flex-column gap-3">
                                <div class="white-stock-card">
                                    <div class="white-stock-card__label">Có thể bán</div>
                                    <div class="white-stock-card__value text-success">
                                        {{ number_format($stats['available']) }}
                                    </div>
                                </div>
                                <div class="white-stock-card">
                                    <div class="white-stock-card__label">Đang giữ chỗ</div>
                                    <div class="white-stock-card__value text-warning">
                                        {{ number_format($stats['reserved']) }}
                                    </div>
                                </div>
                                <div class="white-stock-card">
                                    <div class="white-stock-card__label">Đã bán</div>
                                    <div class="white-stock-card__value text-info">
                                        {{ number_format($stats['sold']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Import hàng loạt</h5>
                            <form action="{{ route('admin.white-accounts.import') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Chọn lô <span class="text-danger">*</span></label>
                                    <select name="batch_id" class="select @error('batch_id') is-invalid @enderror">
                                        <option value="">-- Chọn lô --</option>
                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch->id }}"
                                                {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                                {{ $batch->name }} ({{ $batch->account_type === 'white' ? 'Trắng' : 'Reroll' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('batch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Giá áp dụng (đ)</label>
                                    <input type="number" name="unit_price"
                                        class="form-control @error('unit_price') is-invalid @enderror"
                                        value="{{ old('unit_price') }}" min="0"
                                        placeholder="Để trống dùng giá mặc định của lô">
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Danh sách tài khoản <span class="text-danger">*</span></label>
                                    <textarea name="accounts_data" rows="8" class="form-control @error('accounts_data') is-invalid @enderror"
                                        placeholder="Mỗi dòng một tài khoản, định dạng: username|password|gia_tuy_chon&#10;Ví dụ: user1|pass1|12000">{{ old('accounts_data') }}</textarea>
                                    @error('accounts_data')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Nếu không nhập giá trên từng dòng, hệ thống sẽ lấy giá ở ô "Giá áp dụng"; nếu ô đó
                                        trống sẽ lấy giá mặc định của lô.
                                    </small>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-submit">Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form method="GET" class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Lô</label>
                                            <select name="batch_id" class="form-control">
                                                <option value="">Tất cả</option>
                                                @foreach ($batches as $batch)
                                                    <option value="{{ $batch->id }}"
                                                        {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                                        {{ $batch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Trạng thái</label>
                                            <select name="status" class="form-control">
                                                <option value="">Tất cả</option>
                                                <option value="available"
                                                    {{ request('status') == 'available' ? 'selected' : '' }}>Có thể bán
                                                </option>
                                                <option value="reserved"
                                                    {{ request('status') == 'reserved' ? 'selected' : '' }}>Đang giữ
                                                </option>
                                                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Đã
                                                    bán
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Tìm kiếm</label>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Theo ID hoặc username"
                                                value="{{ request('search') }}">
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary me-2">Lọc</button>
                                            <a href="{{ route('admin.white-accounts.index') }}" class="btn btn-secondary">Xóa
                                                lọc</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Lô</th>
                                            <th>Tài khoản</th>
                                            <th>Mật khẩu</th>
                                            <th>Giá</th>
                                            <th>Trạng thái</th>
                                            <th>Người mua</th>
                                            <th>Ngày tạo</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($accounts as $account)
                                            <tr>
                                                <td>{{ $account->id }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ $account->batch?->name }}</div>
                                                    <small class="text-muted">{{ strtoupper($account->batch?->account_type ?? '-') }}</small>
                                                </td>
                                                <td><code>{{ $account->account_name }}</code></td>
                                                <td><code>{{ $account->password }}</code></td>
                                                <td>{{ number_format($account->unit_price ?? 0) }}đ</td>
                                                <td>
                                                    <span
                                                        class="badges {{ $account->status === 'available' ? 'bg-success' : ($account->status === 'reserved' ? 'bg-warning' : 'bg-info') }}">
                                                        {{ $account->status === 'available' ? 'Có thể bán' : ($account->status === 'reserved' ? 'Đang giữ' : 'Đã bán') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($account->buyer_id)
                                                        #{{ $account->buyer_id }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $account->created_at?->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if ($account->status === 'available')
                                                        <form
                                                            action="{{ route('admin.white-accounts.destroy', $account) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Xóa tài khoản này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">Xóa</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Không có tài khoản nào phù hợp</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $accounts->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .white-stock-card {
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
        }

        .white-stock-card__label {
            font-size: 13px;
            color: #b5b5c3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .white-stock-card__value {
            font-size: 26px;
            font-weight: 700;
        }
    </style>
@endpush

