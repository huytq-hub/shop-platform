@extends('layouts.admin.app')
@section('title', $title)
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>{{ $title }}</h4>
                    <h6>Quản lý lô acc trắng và reroll</h6>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Tạo lô mới</h5>
                            <form action="{{ route('admin.white-account-batches.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Tên lô <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="Ví dụ: Garena trắng tháng 11">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Loại tài khoản</label>
                                    <select name="account_type"
                                        class="select @error('account_type') is-invalid @enderror">
                                        <option value="white" {{ old('account_type') == 'white' ? 'selected' : '' }}>Trắng
                                            100%</option>
                                        <option value="reroll_white"
                                            {{ old('account_type') == 'reroll_white' ? 'selected' : '' }}>Đã reroll (qua
                                            tân thủ)</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Giá mặc định (đ)</label>
                                    <input type="number" name="default_price"
                                        class="form-control @error('default_price') is-invalid @enderror"
                                        value="{{ old('default_price') ?? 0 }}" min="0">
                                    @error('default_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Hình thức đăng nhập</label>
                                    <input type="text" name="login_method"
                                        class="form-control @error('login_method') is-invalid @enderror"
                                        value="{{ old('login_method', 'Garena') }}" placeholder="Garena, Facebook...">
                                    @error('login_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Ngưỡng cảnh báo tồn</label>
                                    <input type="number" name="stock_warning_threshold"
                                        class="form-control @error('stock_warning_threshold') is-invalid @enderror"
                                        value="{{ old('stock_warning_threshold', 0) }}" min="0">
                                    @error('stock_warning_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                                        placeholder="Ví dụ: Batch reroll đã chạy nhiệm vụ tân thủ">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="custom-check">
                                        <input type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}>
                                        <span class="checkmark"></span> Hiển thị cho người dùng
                                    </label>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-submit">Tạo lô</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Danh sách lô</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tên lô</th>
                                            <th>Loại</th>
                                            <th>Giá mặc định</th>
                                            <th>Tồn kho</th>
                                            <th>Đã bán</th>
                                            <th>Hiển thị</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($batches as $batch)
                                            <tr>
                                                <td>{{ $batch->id }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ $batch->name }}</div>
                                                    <small class="text-muted">Login:
                                                        {{ $batch->login_method }}</small>
                                                </td>
                                                <td>
                                                    {{ $batch->account_type === 'white' ? 'Acc trắng' : 'Acc reroll' }}
                                                </td>
                                                <td>{{ number_format($batch->default_price) }}đ</td>
                                                <td>
                                                    {{ $batch->available_accounts }}
                                                    @if ($batch->stock_warning_threshold > 0 && $batch->available_accounts <= $batch->stock_warning_threshold)
                                                        <span class="badges bg-danger ms-1">Thấp</span>
                                                    @endif
                                                </td>
                                                <td>{{ $batch->sold_accounts }}</td>
                                                <td>
                                                    <span
                                                        class="badges {{ $batch->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $batch->is_active ? 'Đang hiển thị' : 'Ẩn' }}
                                                    </span>
                                                </td>
                                                <td class="d-flex gap-2">
                                                    <form action="{{ route('admin.white-account-batches.toggle', $batch) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $batch->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                            {{ $batch->is_active ? 'Ẩn' : 'Hiện' }}
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('admin.white-account-batches.edit', $batch) }}"
                                                        class="btn btn-sm btn-outline-primary">Sửa</a>
                                                    <form action="{{ route('admin.white-account-batches.destroy', $batch) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Xóa lô này? Chỉ khi không còn acc trong lô.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Chưa có lô nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection





