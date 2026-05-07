{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="card w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🚨</div>
            <h2 class="text-2xl font-bold text-gray-800">Đăng nhập</h2>
            <p class="text-gray-600 mt-2">Nhập số điện thoại và mật khẩu</p>
        </div>
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">📱 Số điện thoại</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" 
                       class="input-field" 
                       placeholder="0987654321" 
                       required autofocus>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">🔒 Mật khẩu</label>
                <input type="password" name="password" 
                       class="input-field" 
                       placeholder="••••••" 
                       required>
            </div>
            
            <div class="mb-4 flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">Ghi nhớ đăng nhập</span>
                </label>
            </div>
            
            <button type="submit" class="btn-primary w-full">
                Đăng nhập
            </button>
            
            <div class="text-center mt-4">
                <span class="text-gray-600">Chưa có tài khoản?</span>
                <a href="{{ route('register') }}" class="text-red-600 font-semibold ml-2">
                    Đăng ký ngay
                </a>
            </div>
        </form>
    </div>
</div>
@endsection