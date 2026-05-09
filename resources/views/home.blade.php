{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    /* ===== FONT CHỮ CHÍNH ===== */
    /* Font cho tiêu đề lớn - dùng Montserrat Extra Bold */
    .font-display {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        letter-spacing: 0.05em;
        text-transform: uppercase;
            display: inline; /* hoặc inline-block */

    }
    
    /* Font cho tiêu đề phụ */
    .font-heading {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        letter-spacing: -0.02em;
    }
    
    /* Font cho text thường */
    .font-body {
        font-family: 'Inter', sans-serif;
        font-weight: 400;
    }
    
    /* ===== HERO TEXT ===== */
    .hero-text {
        font-size: clamp(3rem, 8vw, 6.5rem);
        line-height: 1.05;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        letter-spacing: -0.04em;
    }
    

    .hero-text span {
    display: inline; /* hoặc inline-block */
    white-space: nowrap; /* Ngăn không cho xuống dòng */
    }
    /* Chữ nhỏ phía trên hero */
    .hero-label {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.875rem;
        letter-spacing: 0.3em;
        text-transform: uppercase;
    }
    
    /* ===== SECTION TITLE LARGE ===== */
    .section-title-large {
        font-family: 'Montserrat', sans-serif;
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1.1;
        text-transform: uppercase;
    }
    
    /* ===== SECTION SUBTITLE ===== */
    .section-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 1.125rem;
        font-weight: 400;
        letter-spacing: -0.01em;
        line-height: 1.6;
    }
    
    /* ===== STAT NUMBERS ===== */
    .stat-number {
        font-family: 'Montserrat', sans-serif;
        font-size: clamp(3rem, 6vw, 5rem);
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1;
    }
    
    /* ===== CARD TITLES ===== */
    .card-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    
    /* ===== BUTTON TEXT ===== */
    .btn-text {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    
    /* ===== LABEL TRACKING ===== */
    .tracking-label {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.25em;
        text-transform: uppercase;
    }
    
    /* ===== HOVER EFFECTS ===== */
    .hover-scale {
        transition: transform 0.3s ease;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
    
    /* ===== IMAGE PLACEHOLDER ===== */
    .image-placeholder {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
    }
    
    /* ===== BACKGROUND PATTERN ===== */
    .bg-pattern {
        background-color: #0a0a0a;
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(220, 38, 38, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(220, 38, 38, 0.03) 0%, transparent 50%);
    }
</style>

<!-- Tính toán stats trực tiếp trong view -->
@php
    $totalReports = \App\Models\Report::count();
    $todayReports = \App\Models\Report::whereDate('created_at', today())->count();
    $resolvedReports = \App\Models\Report::where('status', 'completed')->count();
    $fakeReports = \App\Models\Report::where('ai_label', 'FAKE')->count();
    $categories = \App\Models\IncidentCategory::count();
    
    $stats = [
        'total_reports' => $totalReports,
        'today_reports' => $todayReports,
        'resolved_reports' => $resolvedReports,
        'fake_reports' => $fakeReports,
        'categories' => $categories,
    ];
    
    $recentReports = \App\Models\Report::with('category')
        ->where('status', 'completed')
        ->latest()
        ->take(6)
        ->get();
    
    $allCategories = \App\Models\IncidentCategory::where('is_active', true)->get();
@endphp

<!-- ============================================ -->
<!-- SECTION 1: HERO - "INSIDE THE WORLD OF FIREFIGHTING" -->
<!-- ============================================ -->
<div class="relative min-h-screen flex items-center overflow-hidden bg-pattern">
    <div class="absolute inset-0 bg-cover bg-center opacity-25"
         style="background-image: url('{{ asset('images/bak11-mp.jpg') }}');">
    </div>
    
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <!-- Label nhỏ phía trên -->
                <div class="tracking-label text-red-500 mb-6">
                    HỆ THỐNG BÁO CÁO SỰ CỐ
                </div>
                
                <!-- Tiêu đề chính - CHỮ LỚN ĐẬM -->
            <h1 class="hero-text text-white mb-6">
    <span>BẢO VỆ</span>
    <span class="text-red-500">CỘNG ĐỒNG</span>
</h1>
                
                <p class="section-subtitle text-gray-300 mb-8">
                    Nhanh chóng - Chính xác - Tin cậy 24/7
                </p>
                
                @guest
                    <div class="flex gap-4">
                        <a href="{{ route('register') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg btn-text text-lg transition">
                            ĐĂNG KÝ NGAY
                        </a>
                        <a href="{{ route('login') }}" 
                           class="border-2 border-white text-white px-8 py-4 rounded-lg btn-text text-lg hover:bg-white/10 transition">
                            ĐĂNG NHẬP
                        </a>
                    </div>
                @endguest
            </div>
            
            <
        </div>
    </div>
</div>
<!-- ============================================ -->
<!-- SECTION 2: BA CỘT LỚN - TĂNG CHIỀU CAO -->
<!-- ============================================ -->
<style>
    .feature-column {
        min-height: 420px; /* Tăng chiều cao tối thiểu */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    /* Thêm khoảng cách cho text */
    .feature-description {
        min-height: 60px; /* Đảm bảo mô tả có chiều cao tối thiểu */
        line-height: 1.8;
    }
</style>

<div class="bg-white py-28">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-10">
            
            <!-- Cột 1 -->
            <div class="feature-column text-center p-12 group hover:bg-gray-50 rounded-2xl transition-all duration-300 hover:shadow-xl">
                <div class="w-32 h-32 mx-auto mb-10 rounded-full overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                    <img src="{{ asset('images/tieu1.png') }}" 
                         alt="Xây dựng niềm tin" 
                         class="w-full h-full object-cover">
                </div>
                
                <h3 class="font-display text-2xl text-gray-900 mb-5 leading-tight">
                    XÂY DỰNG<br>NIỀM TIN
                </h3>
                
                <p class="text-gray-600 text-base leading-relaxed max-w-sm mx-auto feature-description">
                    Minh bạch và trung thực trong mọi báo cáo từ cộng đồng, đảm bảo thông tin chính xác và kịp thời
                </p>
            </div>
            
            <!-- Cột 2 -->
            <div class="feature-column text-center p-12 group hover:bg-gray-50 rounded-2xl transition-all duration-300 hover:shadow-xl border-x border-gray-100">
                <div class="w-32 h-32 mx-auto mb-10 rounded-full overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                    <img src="{{ asset('images/tieu2.png') }}" 
                         alt="Cứu sống con người" 
                         class="w-full h-full object-cover">
                </div>
                
                <h3 class="font-display text-2xl text-gray-900 mb-5 leading-tight">
                    CỨU SỐNG<br>CON NGƯỜI
                </h3>
                
                <p class="text-gray-600 text-base leading-relaxed max-w-sm mx-auto feature-description">
                    Phản ứng nhanh chóng trước mọi sự cố khẩn cấp, đội ngũ luôn sẵn sàng hỗ trợ 24/7
                </p>
            </div>
            
            <!-- Cột 3 -->
            <div class="feature-column text-center p-12 group hover:bg-gray-50 rounded-2xl transition-all duration-300 hover:shadow-xl">
                <div class="w-32 h-32 mx-auto mb-10 rounded-full overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:scale-110">
                    <img src="{{ asset('images/tieu3.png') }}" 
                         alt="An toàn cộng đồng" 
                         class="w-full h-full object-cover">
                </div>
                
                <h3 class="font-display text-2xl text-gray-900 mb-5 leading-tight">
                    AN TOÀN<br>CỘNG ĐỒNG
                </h3>
                
                <p class="text-gray-600 text-base leading-relaxed max-w-sm mx-auto feature-description">
                    Cam kết bảo vệ an toàn cho mọi người dân, xây dựng môi trường sống an lành
                </p>
            </div>
            
        </div>
    </div>
</div>
<!-- ============================================ -->
<!-- SECTION 3: "WHAT WE OFFER" - ĐÃ ĐIỀU CHỈNH -->
<!-- ============================================ -->
<div class="bg-gray-50 py-24">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            
            <!-- CỘT TRÁI: NỘI DUNG -->
            <div>
                <!-- Label nhỏ -->
                <div class="tracking-label text-red-500 mb-6">
                    DỊCH VỤ CỦA CHÚNG TÔI
                </div>
                
                <!-- Tiêu đề chính - "NHỮNG GÌ CHÚNG TÔI" TRÊN 1 DÒNG -->
                <h2 class="font-display text-gray-900 mb-6" style="font-size: clamp(2.2rem, 5vw, 3.8rem); line-height: 1.1; font-weight: 900; letter-spacing: -0.03em; text-transform: uppercase;">
                    MANG ĐẾN CHO<br>NGƯỜI DÙNG 
                </h2>
                
                <!-- Mô tả ngắn gọn -->
                <p class="text-gray-600 text-base leading-relaxed mb-10 max-w-lg">
                    Hệ thống toàn diện giúp báo cáo và xử lý sự cố nhanh chóng, chính xác.
                </p>
                
                <!-- Danh sách tính năng -->
                <div class="space-y-7">
                    
                    <!-- Tính năng 1: Ghi âm -->
                    <div class="flex gap-4 group">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-red-200 transition-all duration-300 group-hover:scale-110">
                            🎙️
                        </div>
                        <div>
                            <h4 class="font-heading text-lg text-gray-900 mb-1">GHI ÂM TRỰC TIẾP</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Mô tả sự cố bằng giọng nói ngay tại hiện trường</p>
                        </div>
                    </div>
                    
                    <!-- Tính năng 2: AI -->
                    <div class="flex gap-4 group">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-red-200 transition-all duration-300 group-hover:scale-110">
                            🤖
                        </div>
                        <div>
                            <h4 class="font-heading text-lg text-gray-900 mb-1">AI PHÁT HIỆN DEEPFAKE</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Phân tích và phát hiện giọng nói giả mạo với độ chính xác cao</p>
                        </div>
                    </div>
                    
                    <!-- Tính năng 3: Định vị -->
                    <div class="flex gap-4 group">
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 group-hover:bg-red-200 transition-all duration-300 group-hover:scale-110">
                            📍
                        </div>
                        <div>
                            <h4 class="font-heading text-lg text-gray-900 mb-1">ĐỊNH VỊ GPS</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Xác định chính xác vị trí sự cố để phản ứng kịp thời</p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- CỘT PHẢI: ẢNH -->
            <div class="relative">
                <!-- Khung ảnh chính -->
                <div class="rounded-3xl overflow-hidden shadow-2xl relative">
                    <img src="{{ asset('images/back2-r2.jpg') }}" 
                         alt="Hệ thống báo cáo sự cố" 
                         class="w-full h-auto object-cover"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    
                    <!-- Fallback -->
                    
                </div>
                
                <!-- Badge nổi -->
                <div class="absolute -top-4 -right-4 bg-red-600 text-white px-5 py-2.5 rounded-2xl shadow-2xl">
                    <div class="text-center">
                        <div class="text-xl font-display">24/7</div>
                        <div class="text-[10px] font-medium tracking-wider">HOẠT ĐỘNG</div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SECTION 4: "HOW WE WORK" + STATS -->
<!-- ============================================ -->
<div class="bg-gray-900 text-white py-24">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
            <div class="order-2 md:order-1">
                <div class="image-placeholder rounded-2xl overflow-hidden">
                    <div class="bg-cover bg-center w-full h-80 opacity-50"
                         style="background-image: url('{{ asset('images/back3.jpg') }}');">
                    </div>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <div class="text-red-500 font-semibold text-sm mb-4 tracking-[0.2em] uppercase">QUY TRÌNH LÀM VIỆC</div>
                
                <!-- THÊM mb-10 ĐỂ GIÃN KHOẢNG CÁCH -->
                <h2 class="font-display section-title-large mb-10" style="white-space: nowrap;">
                    CÁCH CHÚNG TÔI<br>LÀM VIỆC
                </h2>
                
                <div class="space-y-6">
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">1</div>
                        <div>
                            <h4 class="font-bold text-lg">Gửi báo cáo</h4>
                            <p class="text-gray-400">Người dân gửi báo cáo kèm ghi âm và vị trí</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">2</div>
                        <div>
                            <h4 class="font-bold text-lg">AI phân tích</h4>
                            <p class="text-gray-400">Hệ thống tự động phân tích phát hiện giả mạo</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">3</div>
                        <div>
                            <h4 class="font-bold text-lg">Xử lý & phản hồi</h4>
                            <p class="text-gray-400">Admin tiếp nhận và xử lý báo cáo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center pt-12 border-t border-gray-800">
            <div class="hover-scale">
                <div class="stat-number text-red-500">{{ number_format($stats['total_reports']) }}+</div>
                <div class="text-gray-400 text-sm mt-2">Tổng báo cáo</div>
            </div>
            <div class="hover-scale">
                <div class="stat-number text-red-500">{{ number_format($stats['resolved_reports']) }}+</div>
                <div class="text-gray-400 text-sm mt-2">Đã xử lý</div>
            </div>
            <div class="hover-scale">
                <div class="stat-number text-red-500">{{ number_format($stats['fake_reports']) }}+</div>
                <div class="text-gray-400 text-sm mt-2">Phát hiện giả</div>
            </div>
            <div class="hover-scale">
                <div class="stat-number text-red-500">{{ number_format(round(($stats['fake_reports'] / max($stats['total_reports'], 1)) * 100)) }}%</div>
                <div class="text-gray-400 text-sm mt-2">Tỷ lệ phát hiện</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SECTION 5: "STORIES" - BÁO CÁO GẦN ĐÂY -->
<!-- ============================================ -->
<div class="bg-white py-24">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <div class="text-red-500 font-semibold text-sm mb-4 tracking-[0.2em] uppercase">CÂU CHUYỆN</div>
            <h2 class="font-display section-title-large text-gray-900">
                Những báo cáo gần đây
            </h2>
        </div>
        
        @if($recentReports->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentReports as $report)
                    <div class="group bg-gray-50 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all hover:-translate-y-1">
                        <div class="h-1.5" style="background-color: {{ $report->category->color_code ?? '#dc2626' }}"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                                     style="background-color: {{ $report->category->color_code ?? '#dc2626' }}20">
                                    @if($report->category->name == 'Cháy nổ') 🔥
                                    @elseif($report->category->name == 'Tai nạn giao thông') 🚗
                                    @elseif($report->category->name == 'Trộm cắp') 👮
                                    @else 📌
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $report->category->name }}</h4>
                                    <p class="text-xs text-gray-400">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($report->address_text)
                                <p class="text-gray-600 text-sm mb-3">📍 {{ \Str::limit($report->address_text, 60) }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-full">
                                    ✅ Đã xử lý
                                </span>
                                <span class="text-xs text-gray-400">ID: #{{ $report->id }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-gray-50 rounded-2xl">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-gray-500">Chưa có báo cáo nào được xử lý</p>
            </div>
        @endif
    </div>
</div>

<!-- ============================================ -->
<!-- SECTION 6: "A SPOTLIGHT" -->
<!-- ============================================ -->
<div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-24">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="text-red-500 font-semibold text-sm mb-4 tracking-[0.2em] uppercase">TIÊU ĐIỂM</div>
                <h2 class="font-display text-4xl md:text-5xl font-bold mb-6">
                    Công nghệ AI<br>phát hiện Deepfake
                </h2>
                <p class="text-gray-300 text-lg mb-6">
                    Hệ thống sử dụng trí tuệ nhân tạo để phân tích và phát hiện giọng nói giả mạo với độ chính xác cao.
                </p>
                <div class="flex gap-3">
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">Độ chính xác 95%</span>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm">Xử lý nhanh</span>
                    <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm">Bảo mật</span>
                </div>
            </div>
            
            <div class="bg-white/5 rounded-2xl p-8 backdrop-blur-sm border border-white/10">
                <div class="text-center">
                    <div class="text-7xl mb-4">🎯</div>
                    <p class="text-gray-300">AI Detection System</p>
                    <div class="flex justify-center gap-4 mt-4">
                        <span class="px-4 py-2 bg-green-500/20 rounded-lg text-green-400">REAL</span>
                        <span class="px-4 py-2 bg-red-500/20 rounded-lg text-red-400">FAKE</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SECTION 7: "NEED US? 24 HOURS" -->
<!-- ============================================ -->
<div class="bg-red-600 py-24">
    <div class="container mx-auto px-4 text-center">
        <div class="text-6xl mb-6">🆘</div>
        <h2 class="font-display text-4xl md:text-6xl font-bold text-white mb-4">
            Cần chúng tôi?
        </h2>
        <p class="text-2xl text-red-100 mb-8">Luôn sẵn sàng hỗ trợ 24/7</p>
        
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}" class="bg-white text-red-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
                    Đăng ký ngay
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-white/10 transition">
                    Đăng nhập
                </a>
            @endguest
            @auth
                <a href="{{ route('reports.create') }}" class="bg-white text-red-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
                    📝 Gửi báo cáo ngay
                </a>
                <a href="{{ route('reports.my') }}" class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-white/10 transition">
                    📋 Xem lịch sử
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- SECTION 8: "PRIOR NUMBERS" - LIÊN HỆ KHẨN CẤP -->
<!-- ============================================ -->
<div class="bg-gray-900 py-16">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="text-center md:text-left">
                <div class="text-red-500 font-semibold text-sm mb-2 tracking-[0.2em] uppercase">HOTLINE KHẨN CẤP</div>
                <div class="text-3xl md:text-4xl font-bold text-white">📞 113 - 114 - 115</div>
                <p class="text-gray-400 mt-2">Cảnh sát - Cứu hỏa - Cấp cứu</p>
            </div>
            <div>
                <form class="flex gap-3" action="#" method="POST">
                    @csrf
                    <input type="email" placeholder="Nhập email nhận thông báo" 
                           class="flex-1 px-4 py-3 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 focus:outline-none focus:border-red-500">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
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
@endsection