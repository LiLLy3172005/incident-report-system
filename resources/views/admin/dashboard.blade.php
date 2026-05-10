@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        min-height: 280px;
    }
    .chart-container canvas {
        max-height: 280px;
    }
</style>

<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Tổng quan hệ thống</h1>
            <p class="text-sm text-gray-500 mt-1">Cập nhật: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <!-- Card 1 -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Tổng báo cáo</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalReports) }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-xl">📋</div>
            </div>
            <div class="mt-3 flex items-center gap-1">
                <span class="text-green-500 text-xs font-semibold">+{{ $todayReports }}</span>
                <span class="text-gray-400 text-xs">hôm nay</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border-l-4 border-yellow-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Chờ xử lý</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($pendingReports) }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-xl">⏳</div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $totalReports > 0 ? ($pendingReports/$totalReports)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Đã xử lý</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($completedReports) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-xl">✅</div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $totalReports > 0 ? ($completedReports/$totalReports)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Người dùng</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-xl">👥</div>
            </div>
            <div class="mt-3">
                @if($bannedUsers > 0)
                    <span class="text-red-500 text-xs font-semibold">⚠️ {{ $bannedUsers }} bị khóa</span>
                @else
                    <span class="text-green-500 text-xs font-semibold">✅ Tất cả active</span>
                @endif
            </div>
        </div>
    </div>

    <!-- AI Stats Mini Cards -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-5 text-center border border-red-200">
            <div class="text-3xl mb-1">❌</div>
            <div class="text-3xl font-bold text-red-600">{{ $fakeReports }}</div>
            <div class="text-sm font-semibold text-red-500 uppercase tracking-wider mt-1">Fake</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-5 text-center border border-green-200">
            <div class="text-3xl mb-1">✅</div>
            <div class="text-3xl font-bold text-green-600">{{ $realReports }}</div>
            <div class="text-sm font-semibold text-green-500 uppercase tracking-wider mt-1">Real</div>
        </div>
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-5 text-center border border-gray-200">
            <div class="text-3xl mb-1">⏳</div>
            <div class="text-3xl font-bold text-gray-500">{{ $untestedReports }}</div>
            <div class="text-sm font-semibold text-gray-400 uppercase tracking-wider mt-1">Untested</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Bar Chart -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800">📊 Báo cáo theo danh mục</h3>
                <span class="text-xs text-gray-400">Tổng: {{ $totalReports }}</span>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800">🥧 Kết quả AI Detection</h3>
            </div>
            <div class="chart-container" style="max-width: 280px; margin: 0 auto;">
                <canvas id="aiChart"></canvas>
            </div>
            <!-- Legend custom -->
            <div class="flex justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <span class="text-xs text-gray-600">FAKE ({{ $fakeReports }})</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="text-xs text-gray-600">REAL ({{ $realReports }})</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                    <span class="text-xs text-gray-600">UNTESTED ({{ $untestedReports }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800">📈 Xu hướng 7 ngày qua</h3>
            <span class="text-xs text-gray-400">Đơn vị: báo cáo/ngày</span>
        </div>
        <div class="chart-container" style="max-height: 200px;">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <!-- Recent Reports Table -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800">📋 Báo cáo gần đây</h3>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 rounded-lg">
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase">Người gửi</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase">Danh mục</th>
                        <th class="p-3 text-center text-xs font-semibold text-gray-500 uppercase">AI</th>
                        <th class="p-3 text-center text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase">Thời gian</th>
                        <th class="p-3 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentReports as $report)
                    <tr class="hover:bg-gray-50/50 transition
                        {{ $report->ai_label === 'FAKE' ? 'bg-red-50/30' : '' }}
                        {{ $report->ai_label === 'REAL' ? 'bg-green-50/30' : '' }}">
                        <td class="p-3">
                            <span class="font-mono text-xs font-bold text-gray-500">#{{ $report->id }}</span>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($report->user?->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-700 text-xs">{{ $report->user?->name ?? 'Ẩn danh' }}</span>
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium text-white"
                                  style="background-color: {{ $report->category->color_code ?? '#808080' }}">
                                {{ $report->category->name }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $report->ai_label === 'FAKE' ? 'bg-red-100 text-red-700' : 
                                   ($report->ai_label === 'REAL' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ $report->ai_label }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($report->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $report->status === 'pending' ? 'Chờ' : ($report->status === 'completed' ? 'Duyệt' : 'Từ chối') }}
                            </span>
                        </td>
                        <td class="p-3 text-xs text-gray-400">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-center">
                            <a href="{{ route('admin.reports.show', $report->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                                Chi tiết →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === BIỂU ĐỒ CỘT ===
    const categoryData = @json($reportsByCategory);
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                data: categoryData.map(c => c.reports_count),
                backgroundColor: [
                    '#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6'
                ],
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 32,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });

    // === BIỂU ĐỒ TRÒN ===
    const aiCtx = document.getElementById('aiChart').getContext('2d');
    new Chart(aiCtx, {
        type: 'doughnut',
        data: {
            labels: ['FAKE', 'REAL', 'UNTESTED'],
            datasets: [{
                data: [{{ $fakeReports }}, {{ $realReports }}, {{ $untestedReports }}],
                backgroundColor: ['#ef4444', '#22c55e', '#d1d5db'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // === BIỂU ĐỒ ĐƯỜNG ===
    const dailyData = @json($reportsByDay);
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    
    // Gradient fill
    const gradient = dailyCtx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(220, 38, 38, 0.15)');
    gradient.addColorStop(1, 'rgba(220, 38, 38, 0)');
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Báo cáo',
                data: dailyData.map(d => d.count),
                borderColor: '#dc2626',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#dc2626',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection