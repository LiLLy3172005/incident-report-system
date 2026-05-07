{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Trang chủ - Hệ thống báo cáo sự cố')

@section('content')
<!-- Hero Section - Trong suốt -->
<div class="container mx-auto px-4 py-12">
    <div class="text-center text-white mb-12">
        <div class="text-6xl mb-4">🚨</div>
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Hệ thống báo cáo sự cố cộng đồng</h1>
        <p class="text-xl opacity-90">Nhanh chóng - Chính xác - An toàn</p>
        
        @guest
            <div class="flex justify-center gap-4 mt-8">
                <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Đăng ký ngay
                </a>
                <a href="{{ route('login') }}" class="bg-white/20 backdrop-blur-sm border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/30 transition">
                    Đăng nhập
                </a>
            </div>
        @endguest
            
            @auth
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('reports.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition text-center">
                        📝 Gửi báo cáo ngay
                    </a>
                    <a href="{{ route('reports.my') }}" class="bg-transparent border-2 border-white hover:bg-white hover:text-green-800 text-white px-8 py-3 rounded-lg font-semibold transition text-center">
                        📋 Lịch sử báo cáo
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- Thống kê nhanh - 4 cards như ảnh -->
<div class="container mx-auto px-4 -mt-8 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-xl p-5 text-center hover:shadow-2xl transition">
            <div class="text-4xl mb-3">📊</div>
            <div class="text-3xl font-bold text-green-700">{{ $stats['total_reports'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Tổng báo cáo</div>
        </div>
        <div class="bg-white rounded-xl shadow-xl p-5 text-center hover:shadow-2xl transition">
            <div class="text-4xl mb-3">📅</div>
            <div class="text-3xl font-bold text-green-700">{{ $stats['today_reports'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Hôm nay</div>
        </div>
        <div class="bg-white rounded-xl shadow-xl p-5 text-center hover:shadow-2xl transition">
            <div class="text-4xl mb-3">✅</div>
            <div class="text-3xl font-bold text-green-700">{{ $stats['resolved_reports'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Đã xử lý</div>
        </div>
        <div class="bg-white rounded-xl shadow-xl p-5 text-center hover:shadow-2xl transition">
            <div class="text-4xl mb-3">⚠️</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['fake_reports'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Báo cáo giả mạo</div>
        </div>
    </div>
</div>

<!-- Dịch vụ / Tính năng chính - Giống phần PROJECT SERVICES -->
<div class="container mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Tính năng chính</h2>
        <p class="text-gray-500 max-w-2xl mx-auto">Hệ thống cung cấp đầy đủ công cụ để báo cáo và xử lý sự cố</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-50 rounded-xl p-6 text-center hover:shadow-lg transition group">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-red-600 transition">
                <div class="text-2xl group-hover:text-white">🎙️</div>
            </div>
            <h3 class="font-bold text-lg mb-2">Ghi âm sự cố</h3>
            <p class="text-gray-500 text-sm">Ghi âm trực tiếp mô tả tình huống bằng giọng nói</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-6 text-center hover:shadow-lg transition group">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-red-600 transition">
                <div class="text-2xl group-hover:text-white">🤖</div>
            </div>
            <h3 class="font-bold text-lg mb-2">AI phát hiện Deepfake</h3>
            <p class="text-gray-500 text-sm">Tự động phát hiện giọng nói giả mạo</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-6 text-center hover:shadow-lg transition group">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-red-600 transition">
                <div class="text-2xl group-hover:text-white">📍</div>
            </div>
            <h3 class="font-bold text-lg mb-2">Định vị GPS</h3>
            <p class="text-gray-500 text-sm">Xác định chính xác vị trí xảy ra sự cố</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-6 text-center hover:shadow-lg transition group">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-red-600 transition">
                <div class="text-2xl group-hover:text-white">👨‍💼</div>
            </div>
            <h3 class="font-bold text-lg mb-2">Quản lý tập trung</h3>
            <p class="text-gray-500 text-sm">Admin theo dõi và xử lý báo cáo</p>
        </div>
    </div>
</div>

<!-- Danh mục sự cố - Giống phần Portfolio -->
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">Danh mục sự cố</h2>
            <p class="text-gray-500">Chọn loại sự cố để báo cáo</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
            @php
                $categories = \App\Models\IncidentCategory::where('is_active', true)->get();
            @endphp
            
            @foreach($categories as $category)
                <div class="bg-white rounded-xl shadow-md p-5 text-center hover:shadow-xl transition cursor-pointer transform hover:-translate-y-1"
                     onclick="scrollToReport()">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl"
                         style="background-color: {{ $category->color_code }}20; color: {{ $category->color_code }}">
                        @if($category->name == 'Cháy nổ') 🔥
                        @elseif($category->name == 'Tai nạn giao thông') 🚗
                        @elseif($category->name == 'Trộm cắp') 👮
                        @elseif($category->name == 'Cây đổ') 🌳
                        @elseif($category->name == 'Ngập lụt') 💧
                        @else 📌
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $category->description ?? 'Sự cố' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- AI Detection Feature - Giống phần In Aotopc so Unvclctoe -->
<div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <div class="text-red-600 font-semibold mb-2">TÍNH NĂNG NỔI BẬT</div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Phát hiện giọng nói giả mạo bằng AI</h2>
            <p class="text-gray-600 mb-6">Hệ thống sử dụng công nghệ Deep Learning tiên tiến để phân tích và phát hiện các báo cáo có dấu hiệu giả mạo, đảm bảo tính xác thực của thông tin.</p>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm">Độ chính xác 95%</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm">Xử lý nhanh chóng</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm">Bảo mật tuyệt đối</span>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-100 to-red-100 rounded-2xl p-8 text-center">
            <div class="text-7xl mb-4">🎯</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">AI Detection</h3>
            <p class="text-gray-600">Tự động phân tích và đánh giá</p>
            <div class="mt-4 flex justify-center gap-4">
                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs">REAL</span>
                <span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs">FAKE</span>
                <span class="px-3 py-1 bg-gray-500 text-white rounded-full text-xs">UNTESTED</span>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action - Giống phần Feexd Gottaj Stivive Repul -->
<div class="bg-gradient-to-r from-green-800 to-red-700 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Sẵn sàng báo cáo sự cố?</h2>
        <p class="text-lg mb-8 opacity-90 max-w-2xl mx-auto">Hãy chung tay xây dựng cộng đồng an toàn bằng cách báo cáo các sự cố bạn gặp phải.</p>
        
        @guest
            <a href="{{ route('register') }}" class="inline-block bg-white text-green-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Đăng ký tài khoản ngay
            </a>
        @endguest
        
        @auth
            <a href="{{ route('reports.create') }}" class="inline-block bg-white text-green-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                📝 Gửi báo cáo ngay
            </a>
        @endauth
    </div>
</div>

<!-- Báo cáo mới nhất - Giống phần Aptite Mittu Irgan Call -->
<div class="container mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-3">Báo cáo mới nhất</h2>
        <p class="text-gray-500">Những sự cố vừa được tiếp nhận và xử lý</p>
    </div>
    
    @php
        $recentReports = \App\Models\Report::with('category')
            ->where('status', 'completed')
            ->latest()
            ->take(6)
            ->get();
    @endphp
    
    @if($recentReports->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recentReports as $report)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="h-2" style="background-color: {{ $report->category->color_code ?? '#dc2626' }}"></div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm"
                                 style="background-color: {{ $report->category->color_code ?? '#dc2626' }}">
                                @if($report->category->name == 'Cháy nổ') 🔥
                                @elseif($report->category->name == 'Tai nạn giao thông') 🚗
                                @else 📌
                                @endif
                            </div>
                            <span class="font-semibold text-gray-800">{{ $report->category->name }}</span>
                            <span class="text-xs text-gray-400 ml-auto">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        @if($report->address_text)
                            <p class="text-sm text-gray-500 mb-3">📍 {{ \Str::limit($report->address_text, 60) }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">
                                ✅ Đã xử lý
                            </span>
                            <span class="text-xs text-gray-400">
                                ID: #{{ $report->id }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-xl p-12 text-center">
            <div class="text-5xl mb-3">📭</div>
            <p class="text-gray-500">Chưa có báo cáo nào được xử lý</p>
        </div>
    @endif
</div>

<script>
function scrollToReport() {
    @auth
        window.location.href = "{{ route('reports.create') }}";
    @else
        Swal.fire({
            title: 'Vui lòng đăng nhập',
            text: 'Bạn cần đăng nhập để gửi báo cáo',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập',
            cancelButtonText: 'Để sau'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            }
        });
    @endauth
}
</script>

<style>
    .scale-hover {
        transition: transform 0.3s ease;
    }
    .scale-hover:hover {
        transform: scale(1.05);
    }
</style>
@endsection