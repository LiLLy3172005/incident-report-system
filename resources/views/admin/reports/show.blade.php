@extends('layouts.admin')

@section('title', 'Chi tiết báo cáo #' . $report->id)

@section('content')
{{-- Mapbox CSS --}}
<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
<style>
    #map { min-height: 300px; border-radius: 0.5rem; }
</style>

<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Quay lại danh sách</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">Chi tiết báo cáo #{{ $report->id }}</h1>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-bold
            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
               ($report->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
            {{ $report->status === 'pending' ? 'Chờ xử lý' : ($report->status === 'completed' ? 'Đã duyệt' : 'Đã từ chối') }}
        </span>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Cột trái: Thông tin -->
        <div class="md:col-span-2 space-y-6">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">📋 Thông tin báo cáo</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Người gửi:</span>
                        <span class="font-medium ml-2">{{ $report->user?->name ?? 'Ẩn danh' }}</span>
                        @if($report->user)
                            <div class="mt-1">
                                <span class="text-gray-500">Strikes:</span>
                                <span class="font-bold {{ $report->user->strikes >= 3 ? 'text-red-600' : 'text-yellow-600' }} ml-2">
                                    {{ $report->user->strikes }}/3
                                </span>
                                @if($report->user->is_banned)
                                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-bold">BANNED</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-500">Danh mục:</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium text-white ml-2"
                              style="background-color: {{ $report->category->color_code ?? '#808080' }}">
                            {{ $report->category->name }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">Thời gian:</span>
                        <span class="ml-2">{{ $report->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">AI Label:</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold ml-2
                            {{ $report->ai_label === 'FAKE' ? 'bg-red-100 text-red-700' : 
                               ($report->ai_label === 'REAL' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $report->ai_label }}
                        </span>
                        @if($report->ai_confidence)
                            <span class="text-xs text-gray-400 ml-1">{{ $report->ai_confidence }}%</span>
                        @endif
                    </div>
                </div>
                @if($report->description)
                <div class="mt-4 pt-4 border-t">
                    <span class="text-gray-500 text-sm">Mô tả:</span>
                    <p class="mt-1 text-gray-800">{{ $report->description }}</p>
                </div>
                @endif
            </div>

            <!-- ============================================ -->
            <!-- AUDIO PLAYER - ĐÃ SỬA -->
            <!-- ============================================ -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">🎙️ Ghi âm</h3>
                @if($report->audio_url)
                    @php
                        $rawUrl = $report->audio_url;
                        
                        // Bỏ domain nếu là URL đầy đủ
                        if (str_contains($rawUrl, 'http://localhost:8000')) {
                            $rawUrl = str_replace('http://localhost:8000', '', $rawUrl);
                        } elseif (str_contains($rawUrl, 'https://')) {
                            $parsed = parse_url($rawUrl);
                            $rawUrl = $parsed['path'] ?? $rawUrl;
                        }
                        
                        // Đảm bảo bắt đầu bằng /
                        $rawUrl = '/' . ltrim($rawUrl, '/');
                        
                        // Tạo URL đầy đủ
                        $fullAudioUrl = url($rawUrl);
                        
                        // Kiểm tra file có tồn tại không
                        $filePath = public_path($rawUrl);
                        $fileExists = file_exists($filePath);
                        $fileSize = $fileExists ? round(filesize($filePath) / 1024, 1) : 0;
                    @endphp
                    
                    {{-- Thông tin file --}}
                    <div class="text-xs mb-3 p-2 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">📁 File:</span>
                        <span class="text-gray-700 font-mono">{{ basename($rawUrl) }}</span>
                        <span class="mx-2">|</span>
                        <span class="text-gray-500">📦 Size:</span>
                        <span class="text-gray-700">{{ $fileSize }} KB</span>
                        @if($fileExists)
                            <span class="ml-2 text-green-600">✅</span>
                        @else
                            <span class="ml-2 text-red-600">❌ File không tồn tại</span>
                        @endif
                    </div>
                    
                    @if($fileExists)
                        <audio controls class="w-full" preload="auto">
                            <source src="{{ $fullAudioUrl }}" type="audio/webm">
                            Trình duyệt của bạn không hỗ trợ phát audio.
                        </audio>
                    @else
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                            ⚠️ File audio không tìm thấy tại: <code class="text-xs">{{ $filePath }}</code>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ $fullAudioUrl }}" 
                           class="text-blue-600 text-sm underline" 
                           target="_blank"
                           download>
                            📥 Tải xuống file audio
                        </a>
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Không có file ghi âm</p>
                @endif
            </div>

            <!-- Bản đồ - MAPBOX MIỄN PHÍ -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">📍 Vị trí</h3>
                @if($report->latitude && $report->longitude)
                    <div id="map" class="w-full rounded-lg border"></div>
                    <p class="text-sm text-gray-500 mt-2">
                        📍 {{ $report->address_text ?? 'Không có địa chỉ' }}
                        <br>
                        <span class="text-xs text-gray-400">
                            Tọa độ: {{ $report->latitude }}, {{ $report->longitude }}
                        </span>
                    </p>
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" 
                       target="_blank" 
                       class="text-blue-600 text-sm underline mt-2 inline-block">
                        Xem trên Google Maps →
                    </a>
                @else
                    <p class="text-gray-400 text-sm">Không có dữ liệu vị trí</p>
                @endif
            </div>
        </div>

        <!-- Cột phải: Hành động + Lịch sử -->
        <div class="space-y-6">
            @if($report->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">⚡ Hành động</h3>
                
                <form action="{{ route('admin.reports.approve', $report->id) }}" method="POST" class="mb-3">
                    @csrf
                    <textarea name="note" rows="2" placeholder="Ghi chú (tùy chọn)" class="w-full border rounded-lg px-3 py-2 text-sm mb-2"></textarea>
                    <button type="submit" class="w-full bg-green-600 text-white rounded-lg px-4 py-2.5 font-medium hover:bg-green-700 transition">
                        ✅ Duyệt báo cáo
                    </button>
                </form>

                <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST">
                    @csrf
                    <textarea name="note" rows="2" placeholder="Lý do từ chối..." class="w-full border rounded-lg px-3 py-2 text-sm mb-2"></textarea>
                    <button type="submit" class="w-full bg-red-600 text-white rounded-lg px-4 py-2.5 font-medium hover:bg-red-700 transition">
                        ❌ Từ chối
                    </button>
                </form>
            </div>
            @endif

            <!-- Lịch sử trạng thái -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">📜 Lịch sử</h3>
                @if($report->statusHistories->count() > 0)
                <div class="space-y-3">
                    @foreach($report->statusHistories as $history)
                    <div class="text-sm border-l-2 border-gray-200 pl-3">
                        <div class="font-medium">
                            <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                {{ $history->new_status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $history->new_status === 'completed' ? 'DUYỆT' : 'TỪ CHỐI' }}
                            </span>
                        </div>
                        <div class="text-gray-500 text-xs mt-1">
                            {{ $history->created_at->format('d/m/Y H:i') }} 
                            @if($history->changedBy)
                                · {{ $history->changedBy->name }}
                            @endif
                        </div>
                        @if($history->note)
                        <p class="text-gray-600 text-xs mt-1">{{ $history->note }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-400 text-sm">Chưa có lịch sử</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MAPBOX - MIỄN PHÍ --}}
@if($report->latitude && $report->longitude)
<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    mapboxgl.accessToken = 'pk.eyJ1IjoiaHV5bmhsdXVseSIsImEiOiJjbW95MHdjeGwwMHA2MnFwd3Z6MDRocDl4In0.-bFIJFLNLJ4aO-Y33GP3aA';
    
    const lng = {{ $report->longitude }};
    const lat = {{ $report->latitude }};
    
    const mapEl = document.getElementById('map');
    if (!mapEl) return;
    
    try {
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [lng, lat],
            zoom: 15,
        });
        
        map.addControl(new mapboxgl.NavigationControl(), 'top-right');
        
        new mapboxgl.Marker({ color: '#dc2626' })
            .setLngLat([lng, lat])
            .addTo(map);
            
        console.log('Mapbox loaded successfully');
    } catch (error) {
        console.error('Mapbox error:', error);
        mapEl.innerHTML = `
            <div class="flex items-center justify-center h-64 bg-gray-100 rounded-lg">
                <div class="text-center">
                    <p class="text-gray-500 mb-2">📍 Vị trí: ${lat}, ${lng}</p>
                    <a href="https://www.google.com/maps?q=${lat},${lng}" 
                       target="_blank" class="text-blue-600 underline">
                        Xem trên Google Maps →
                    </a>
                </div>
            </div>`;
    }
});
</script>
@endif
@endsection