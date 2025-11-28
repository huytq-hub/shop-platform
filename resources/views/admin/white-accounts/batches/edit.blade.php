@extends('layouts.admin.app')
@section('title', $title)
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>{{ $title }}</h4>
                    <h6>Chỉnh sửa thông tin lô #{{ $batch->id }}</h6>
                </div>
                <div class="page-btn">
                    <a href="{{ route('admin.white-account-batches.index') }}" class="btn btn-added">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.white-account-batches.update', $batch) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Tên lô <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $batch->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Slug (tuỳ chọn)</label>
                                    <input type="text" name="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug', $batch->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Loại tài khoản</label>
                                    <select name="account_type"
                                        class="select @error('account_type') is-invalid @enderror">
                                        <option value="white" {{ old('account_type', $batch->account_type) == 'white' ? 'selected' : '' }}>Trắng 100%</option>
                                        <option value="reroll_white" {{ old('account_type', $batch->account_type) == 'reroll_white' ? 'selected' : '' }}>Đã reroll (qua tân thủ)</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Hình thức đăng nhập</label>
                                    <input type="text" name="login_method"
                                        class="form-control @error('login_method') is-invalid @enderror"
                                        value="{{ old('login_method', $batch->login_method) }}">
                                    @error('login_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Giá mặc định (đ)</label>
                                    <input type="number" name="default_price"
                                        class="form-control @error('default_price') is-invalid @enderror"
                                        value="{{ old('default_price', $batch->default_price) }}" min="0">
                                    @error('default_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Ngưỡng cảnh báo tồn</label>
                                    <input type="number" name="stock_warning_threshold"
                                        class="form-control @error('stock_warning_threshold') is-invalid @enderror"
                                        value="{{ old('stock_warning_threshold', $batch->stock_warning_threshold) }}" min="0">
                                    @error('stock_warning_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note" rows="4" class="form-control @error('note') is-invalid @enderror">{{ old('note', $batch->note) }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="custom-check">
                                        <input type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $batch->is_active) ? 'checked' : '' }}>
                                        <span class="checkmark"></span> Hiển thị lô này cho người dùng
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit me-2">Cập nhật</button>
                            <a href="{{ route('admin.white-account-batches.index') }}" class="btn btn-cancel">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection





