@extends('layouts.admin.app')
@section('title', $title)
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Thêm tài khoản game mới</h4>
                    <h6>Nhập thông tin tài khoản game</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.accounts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Danh mục game <span class="text-danger">*</span></label>
                                    <select name="game_category_id"
                                        class="select @error('game_category_id') is-invalid @enderror">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('game_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('game_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Tên tài khoản <span class="text-danger">*</span></label>
                                    <input type="text" name="account_name" value="{{ old('account_name') }}"
                                        class="form-control @error('account_name') is-invalid @enderror">
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="text" name="password" value="{{ old('password') }}"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Giá tiền <span class="text-danger">*</span></label>
                                    <input type="number" name="price" value="{{ old('price') ?? 0 }}"
                                        class="form-control @error('price') is-invalid @enderror">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="status" class="select @error('status') is-invalid @enderror">
                                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Còn
                                            hàng</option>
                                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Đã bán
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Bản phát hành <span class="text-danger">*</span></label>
                                    <select name="account_version"
                                        class="select @error('account_version') is-invalid @enderror">
                                        <option value="vietnam"
                                            {{ old('account_version', 'vietnam') == 'vietnam' ? 'selected' : '' }}>Bản Việt
                                            Nam</option>
                                        <option value="global"
                                            {{ old('account_version') == 'global' ? 'selected' : '' }}>Bản Quốc tế</option>
                                    </select>
                                    @error('account_version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Hình thức login acc <span class="text-danger">*</span></label>
                                    <input type="text" name="login_method" value="{{ old('login_method') }}"
                                        class="form-control @error('login_method') is-invalid @enderror"
                                        placeholder="Garena / Facebook / Email...">
                                    @error('login_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Giá trị đội hình (BP)</label>
                                    <input type="number" name="team_value" value="{{ old('team_value') }}"
                                        class="form-control @error('team_value') is-invalid @enderror" min="0"
                                        placeholder="Ví dụ: 120000000">
                                    @error('team_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Ảnh đại diện</label>
                                    <div class="image-upload">
                                        <input type="file" name="thumb"
                                            class="form-control @error('thumb') is-invalid @enderror" accept="image/*"
                                            onchange="previewImage(this, 'preview-thumb')">
                                        @error('thumb')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="image-uploads">
                                            <img src="{{ asset('assets/img/icons/upload.svg') }}" alt="Upload Image"
                                                style="max-width: 200px; max-height: 200px;">
                                            <h4>Kéo thả hoặc chọn ảnh để tải lên</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Ảnh chi tiết</label>
                                    <div class="image-upload">
                                        <input type="file" name="images[]" multiple
                                            class="form-control @error('images') is-invalid @enderror" accept="image/*"
                                            onchange="previewMultipleImages(this, 'preview-images')">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="image-uploads">
                                            <img src="{{ asset('assets/img/icons/upload.svg') }}" alt="Upload Images"
                                                style="max-width: 200px; max-height: 200px;">
                                            <h4>Kéo thả hoặc chọn nhiều ảnh để tải lên</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <x-preview-image />
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-submit me-2">Thêm mới</button>
                                <a href="{{ route('admin.accounts.index') }}" class="btn btn-cancel">Hủy bỏ</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
