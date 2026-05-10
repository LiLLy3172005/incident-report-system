{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Báo cáo sự cố công cộng')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Cấu hình Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'display': ['Montserrat', 'sans-serif'],
                    },
                    fontWeight: {
                        'extrablack': '900',
                    },
                    colors: {
                        'brand-red': '#dc2626',
                        'brand-dark': '#0f172a',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
        }
        
        .font-heading {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        
        .font-display {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }
        
        .tracking-label {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
        }
        
        .header-transparent {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.4s ease;
            padding: 0.75rem 0;
        }
        
        .header-scrolled {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .logo-container:hover { transform: scale(1.02); }
        
        .logo-img {
            height: 60px;
            width: auto;
            transition: all 0.3s ease;
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #dc2626;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after { width: 100%; }
        
        /* Admin link đặc biệt */
        .nav-link-admin {
            color: #fbbf24 !important; /* Màu vàng gold cho Admin */
            font-weight: 700;
        }
        
        .nav-link-admin::after {
            background-color: #fbbf24 !important;
        }
        
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }
        
        .btn-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        
        .flash-message {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }
        
        [x-cloak] { display: none !important; }
        
        @media (max-width: 768px) {
            .logo-img { height: 32px; }
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Header -->
    <header x-data="{ scrolled: false }" 
            x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
            :class="scrolled ? 'header-scrolled' : 'header-transparent'"
            class="header-transparent">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                
                <!-- Logo -->
                <a href="{{ route('home') }}" class="logo-container group">
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Logo" 
                         class="logo-img"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="text-2xl hidden transform group-hover:scale-110 transition">🚨</span>
                    <span class="text-xl font-display text-white tracking-tight">
                        Báo cáo<span class="text-brand-red"> sự cố</span>
                    </span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    @auth
                        <a href="{{ route('reports.create') }}" class="nav-link text-white/90 hover:text-white text-sm">
                            📝 Gửi báo cáo
                        </a>
                        <a href="{{ route('reports.my') }}" class="nav-link text-white/90 hover:text-white text-sm">
                            📋 Lịch sử
                        </a>
                        <a href="{{ route('heatmap') }}" class="nav-link text-white/90 hover:text-white text-sm">
                             🔥 Bản đồ nhiệt
                        </a>
                        
                        {{-- === ADMIN LINK NỔI BẬT === --}}
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" 
                               class="nav-link nav-link-admin text-sm flex items-center gap-1">
                                <span>⚙️</span> Admin
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center space-x-2 focus:outline-none group">
                                <div class="avatar bg-gradient-to-r from-brand-red to-orange-500 text-white group-hover:scale-105 transition">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden lg:inline text-white/90 text-sm font-heading tracking-normal">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-4 h-4 text-white/70 transition-transform" 
                                     :class="dropdownOpen ? 'rotate-180' : ''" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown -->
                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl z-50 overflow-hidden"
                                 x-cloak>
                                <div class="py-2">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="font-heading text-sm text-gray-800">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->phone }}</p>
                                        @if(auth()->user()->role === 'admin')
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-bold">
                                                ADMIN
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('reports.my') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                        <span class="mr-3 text-lg">📋</span> Lịch sử báo cáo
                                    </a>
                                    <a href="{{ route('reports.create') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition font-medium">
                                        <span class="mr-3 text-lg">📝</span> Gửi báo cáo mới
                                    </a>
                                    
                                    {{-- === ADMIN MENU TRONG DROPDOWN === --}}
                                    @if(auth()->user()->role === 'admin')
                                        <div class="border-t border-gray-100 mt-1 pt-1">
                                            <div class="px-4 py-1 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Admin Panel</div>
                                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition font-medium">
                                                <span class="mr-3 text-lg">📊</span> Dashboard
                                            </a>
                                            <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 transition font-medium">
                                                <span class="mr-3 text-lg">📋</span> Quản lý báo cáo
                                            </a>
                                        </div>
                                    @endif
                                    
                                    <hr class="my-1 border-gray-100">
                                    
                                    {{-- Logout --}}
                                    <a href="{{ route('logout') }}" 
                                       class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                        <span class="mr-3 text-lg">🚪</span> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white/90 hover:text-white text-sm font-heading tracking-normal transition">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-brand-red hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm btn-text transition shadow-lg hover:shadow-xl">
                            Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="min-h-screen">
        @if(session('success'))
            <div class="flash-message fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-pulse">
                <div class="bg-green-500 text-white px-6 py-3 rounded-full shadow-lg flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="flash-message fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-pulse">
                <div class="bg-red-500 text-white px-6 py-3 rounded-full shadow-lg flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-brand-dark/90 backdrop-blur-sm text-white/70 py-8 mt-12">
        <div class="container mx-auto px-4 text-center text-sm font-medium">
            <p>© 2025 Hệ thống báo cáo sự cố cộng đồng. Bảo vệ bởi công nghệ AI.</p>
        </div>
    </footer>
    
    <script>
        // Auto hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.fixed.top-20');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>