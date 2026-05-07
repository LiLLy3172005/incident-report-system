{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="card w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">📝</div>
            <h2 class="text-2xl font-bold text-gray-800">Đăng ký tài khoản</h2>
            <p class="text-gray-600 mt-2">Tham gia báo cáo sự cố cộng đồng</p>
        </div>
        
        <!-- Hiển thị tất cả lỗi validation -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <p class="font-semibold mb-2">Vui lòng sửa các lỗi sau:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Hiển thị success message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">👤 Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="input-field @error('name') input-error @enderror"
                       placeholder="VD: Nguyễn Văn A" 
                       required>
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">📱 Số điện thoại <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" 
                       class="input-field @error('phone') input-error @enderror"
                       placeholder="VD: 0987654321" 
                       required>
                <p class="text-xs text-gray-500 mt-1">Số điện thoại dùng để đăng nhập, phải là số thật (10-11 số)</p>
                @error('phone')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">🔒 Mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="password" 
                       class="input-field @error('password') input-error @enderror"
                       placeholder="Tối thiểu 8 ký tự" 
                       required>
                <p class="text-xs text-gray-500 mt-1">Mật khẩu phải có ít nhất 8 ký tự</p>
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2 font-semibold">🔒 Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" 
                       class="input-field"
                       placeholder="Nhập lại mật khẩu" 
                       required>
            </div>
            
            <button type="submit" class="btn-primary w-full text-center">
                Đăng ký ngay
            </button>
            
            <div class="text-center mt-4">
                <span class="text-gray-600">Đã có tài khoản?</span>
                <a href="{{ route('login') }}" class="text-red-600 font-semibold ml-2 hover:underline">
                    Đăng nhập
                </a>
            </div>
        </form>
        
        <!-- Debug info -->
        @if(old('phone') || $errors->any())
        <div class="mt-4 p-3 bg-gray-100 rounded-lg text-xs">
            <p class="font-semibold mb-1">📌 Thông tin debug:</p>
            <p>Họ tên đã nhập: <strong>{{ old('name') ?: 'chưa nhập' }}</strong></p>
            <p>SĐT đã nhập: <strong>{{ old('phone') ?: 'chưa nhập' }}</strong></p>
            <p>Số lỗi: <strong>{{ $errors->count() }}</strong></p>
            @foreach($errors->keys() as $key)
                <p>- Field <strong>{{ $key }}</strong> bị lỗi</p>
            @endforeach
            <p class="mt-2 text-blue-600">Kiểm tra database: users table đã có bản ghi nào chưa?</p>
        </div>
        @endif
    </div>
</div>
@endsection