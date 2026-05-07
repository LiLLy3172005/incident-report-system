{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Báo cáo sự cố công cộng')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Cấu hình Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green-dark': '#166534',
                        'red-dark': '#991b1b',
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
        /* Background full page với ảnh */
        body {
            background-image: url('{{ asset("images/bak11.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }
        
        /* Lớp phủ mờ để chữ dễ đọc hơn */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        
        /* Đảm bảo nội dung nằm trên lớp phủ */
        nav, main {
            position: relative;
            z-index: 1;
        }
        
        /* Header trong suốt */
        .header-transparent {
            background: transparent !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: none;
        }
        
        /* Card background trong suốt nhẹ */
        .card-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border-radius: 1rem;
        }
        
        /* Nền trắng mờ cho content */
        .content-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            padding: 1rem;
        }
        
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Header trong suốt -->
    <nav class="header-transparent shadow-md fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 text-white">
                    <span class="text-2xl">🚨</span>
                    <span class="text-lg font-bold">Báo cáo sự cố</span>
                </a>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none text-white">
                                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm text-white rounded-full flex items-center justify-center text-sm font-bold border border-white/30">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:inline text-sm text-white">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 text-gray-800"
                                 x-cloak>
                                <a href="{{ route('reports.my') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                    <span class="mr-2">📋</span> Lịch sử
                                </a>
                                <a href="{{ route('reports.create') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                    <span class="mr-2">📝</span> Gửi báo cáo
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-gray-100">
                                        <span class="mr-2">⚙️</span> Admin
                                    </a>
                                @endif
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">
                                        <span class="mr-2">🚪</span> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hover:underline text-white text-sm">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-white/30 transition border border-white/30">
                            Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="pt-16">
        @if(session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>