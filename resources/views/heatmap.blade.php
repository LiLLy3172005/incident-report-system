@extends('layouts.app')

@section('title', 'Bản đồ nhiệt sự cố')

@section('content')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
<style>
    #heatmap-map {
        width: 100%;
        height: calc(100vh - 80px);
        border-radius: 0;
    }
    
    .filter-bar {
        position: absolute;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 12px 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-bar select,
    .filter-bar input {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        outline: none;
        background: white;
    }
    
    .filter-bar select:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
    }
    
    .legend {
        position: absolute;
        bottom: 30px;
        left: 20px;
        z-index: 10;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        font-size: 12px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    
    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 6px rgba(0,0,0,0.3);
    }
    
    .stat-badge {
        position: absolute;
        top: 100px;
        right: 20px;
        z-index: 10;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        font-size: 13px;
    }
    
    .cluster-marker {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .cluster-marker:hover {
        transform: scale(1.1);
    }
    
    @media (max-width: 768px) {
        .filter-bar {
            top: 80px;
            left: 10px;
            right: 10px;
            transform: none;
            padding: 10px;
            gap: 8px;
        }
        .stat-badge {
            top: 180px;
            right: 10px;
            padding: 10px 14px;
            font-size: 11px;
        }
        .legend {
            bottom: 10px;
            left: 10px;
            padding: 12px;
        }
    }
</style>

<div style="position: relative; min-height: calc(100vh - 80px);">
    
    <!-- Filter Bar -->
<div class="filter-bar">
    <span class="text-sm font-bold text-gray-700 mr-2">🔍 Lọc:</span>
    <select id="categoryFilter" onchange="applyFilter()">
        <option value="">Tất cả danh mục</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <select id="statusFilter" onchange="applyFilter()">
        <option value="completed" {{ !request('status') || request('status') == 'completed' ? 'selected' : '' }}>✅ Đã duyệt</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Chờ duyệt</option>
        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Từ chối</option>
    </select>
    <button onclick="resetMap()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
        🔄 Reset
    </button>
</div>
    
    <!-- Stat Badge -->
<div class="stat-badge">
    <div class="font-bold text-gray-800 text-lg" id="totalCount">{{ $reports->count() }}</div>
    <div class="text-gray-500">sự cố đã duyệt trên bản đồ</div>
    @if(request('status') !== 'completed' && request('status') !== null)
        <div class="text-xs text-yellow-600 mt-1">Đang xem: {{ request('status') === 'pending' ? 'Chờ duyệt' : 'Từ chối' }}</div>
    @endif
</div>
    
    <!-- Legend -->
    <div class="legend">
        <div class="font-bold text-gray-700 mb-2">Chú thích</div>
        @foreach($categories as $cat)
        <div class="legend-item">
            <div class="legend-dot" style="background-color: {{ $cat->color_code }}"></div>
            <span class="text-gray-600">{{ $cat->name }}</span>
        </div>
        @endforeach
        <div class="legend-item mt-2 pt-2 border-t border-gray-200">
            <div class="legend-dot" style="background: radial-gradient(circle, rgba(255,0,0,0.8) 0%, rgba(255,0,0,0.2) 70%); width: 18px; height: 18px;"></div>
            <span class="text-gray-600">Điểm nóng (≥3 sự cố)</span>
        </div>
    </div>
    
    <!-- Map -->
    <div id="heatmap-map"></div>
</div>

<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
<script>
mapboxgl.accessToken = 'pk.eyJ1IjoiaHV5bmhsdXVseSIsImEiOiJjbW95MHdjeGwwMHA2MnFwd3Z6MDRocDl4In0.-bFIJFLNLJ4aO-Y33GP3aA';

// Dữ liệu từ server
const allReports = @json($reports);
let filteredReports = [...allReports];
let map;
let markers = [];
let heatCircles = [];

function initMap() {
    map = new mapboxgl.Map({
        container: 'heatmap-map',
        style: 'mapbox://styles/mapbox/dark-v11',
        center: [108.2464, 16.0443], // Đà Nẵng
        zoom: 14,
        pitch: 45,
    });
    
    map.addControl(new mapboxgl.NavigationControl(), 'top-left');
    map.addControl(new mapboxgl.FullscreenControl(), 'top-left');
    
    map.on('load', () => {
        renderHeatmap(filteredReports);
    });
}

function renderHeatmap(reports) {
    // Xóa markers cũ
    markers.forEach(m => m.remove());
    markers = [];
    
    // Xóa heat circles cũ
    if (map.getSource('heat')) {
        map.removeLayer('heat-layer');
        map.removeSource('heat');
    }
    
    if (reports.length === 0) return;
    
    // Nhóm các báo cáo theo vị trí gần nhau
    const clusters = clusterReports(reports, 0.001);
    
    // Tạo GeoJSON cho heatmap
    const heatPoints = [];
    
    clusters.forEach(cluster => {
        const count = cluster.reports.length;
        const lat = cluster.centerLat;
        const lng = cluster.centerLng;
        
        // Thêm điểm heatmap (cường độ = số báo cáo)
        for (let i = 0; i < Math.min(count, 5); i++) {
            heatPoints.push([
                lng + (Math.random() - 0.5) * 0.0005,
                lat + (Math.random() - 0.5) * 0.0005
            ]);
        }
        
        // Thêm marker cho cụm
        if (count >= 1) {
            const el = document.createElement('div');
            el.className = 'cluster-marker';
            el.style.background = cluster.color || '#dc2626';
            el.style.width = Math.min(24 + count * 4, 50) + 'px';
            el.style.height = Math.min(24 + count * 4, 50) + 'px';
            el.innerHTML = count;
            
            const popupContent = `
                <div style="font-family: sans-serif; max-width: 250px;">
                    <h4 style="margin:0 0 8px; font-size:14px;">📍 ${cluster.category}</h4>
                    <p style="margin:0 0 4px; font-size:12px; color:#666;">📊 ${count} sự cố tại khu vực này</p>
                    <p style="margin:0; font-size:11px; color:#999;">${cluster.reports[0].address || 'Không có địa chỉ'}</p>
                    ${count >= 3 ? '<p style="margin:8px 0 0; font-size:11px; color:#dc2626; font-weight:bold;">🔥 ĐIỂM NÓNG</p>' : ''}
                </div>
            `;
            
            const marker = new mapboxgl.Marker({ element: el })
                .setLngLat([lng, lat])
                .setPopup(new mapboxgl.Popup().setHTML(popupContent))
                .addTo(map);
            
            markers.push(marker);
        }
    });
    
    // Thêm heatmap layer
    map.addSource('heat', {
        type: 'geojson',
        data: {
            type: 'FeatureCollection',
            features: heatPoints.map(p => ({
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: p
                }
            }))
        }
    });
    
    map.addLayer({
        id: 'heat-layer',
        type: 'heatmap',
        source: 'heat',
        paint: {
            'heatmap-weight': 0.8,
            'heatmap-intensity': 1.5,
            'heatmap-color': [
                'interpolate', ['linear'], ['heatmap-density'],
                0, 'rgba(33,102,172,0)',
                0.2, 'rgb(103,169,207)',
                0.4, 'rgb(209,229,240)',
                0.6, 'rgb(253,219,199)',
                0.8, 'rgb(239,138,98)',
                1, 'rgb(178,24,43)'
            ],
            'heatmap-radius': 30,
            'heatmap-opacity': 0.7,
        }
    });
}

function clusterReports(reports, threshold) {
    const clusters = [];
    const used = new Set();
    
    for (let i = 0; i < reports.length; i++) {
        if (used.has(i)) continue;
        
        const cluster = {
            reports: [reports[i]],
            centerLat: reports[i].lat,
            centerLng: reports[i].lng,
            category: reports[i].category,
            color: reports[i].color,
        };
        
        used.add(i);
        
        for (let j = i + 1; j < reports.length; j++) {
            if (used.has(j)) continue;
            
            const dist = Math.sqrt(
                Math.pow(reports[j].lat - cluster.centerLat, 2) +
                Math.pow(reports[j].lng - cluster.centerLng, 2)
            );
            
            if (dist < threshold) {
                cluster.reports.push(reports[j]);
                used.add(j);
            }
        }
        
        // Recalculate center
        cluster.centerLat = cluster.reports.reduce((s, r) => s + r.lat, 0) / cluster.reports.length;
        cluster.centerLng = cluster.reports.reduce((s, r) => s + r.lng, 0) / cluster.reports.length;
        
        clusters.push(cluster);
    }
    
    return clusters;
}

function applyFilter() {
    const categoryId = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (categoryId) params.set('category_id', categoryId);
    if (status) params.set('status', status);
    
    window.location.href = '{{ route("heatmap") }}?' + params.toString();
}

function resetMap() {
    window.location.href = '{{ route("heatmap") }}';
}

// Fit map to show all points
function fitBounds() {
    if (filteredReports.length === 0) return;
    
    const bounds = new mapboxgl.LngLatBounds();
    filteredReports.forEach(r => bounds.extend([r.lng, r.lat]));
    
    map.fitBounds(bounds, { padding: 100, maxZoom: 16, duration: 1500 });
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    initMap();
    setTimeout(fitBounds, 1000);
});

// Reload khi back từ filter
document.getElementById('totalCount').textContent = filteredReports.length;
</script>
@endsection