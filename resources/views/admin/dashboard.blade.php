@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Tổng quan hệ thống</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="text-gray-500 text-sm mb-1">Tổng báo cáo</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($totalReports) }}</div>
            <div class="text-xs text-green-500 mt-1">+{{ $todayReports }} hôm nay</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm mb-1">Chờ xử lý</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($pendingReports) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm mb-1">Đã xử lý</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($completedReports) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm mb-1">Người dùng</div>
            <div class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers) }}</div>
            <div class="text-xs text-red-500 mt-1">{{ $bannedUsers }} bị khóa</div>
        </div>
    </div>

    <!-- AI Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $fakeReports }}</div>
            <div class="text-sm text-red-500">FAKE</div>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $realReports }}</div>
            <div class="text-sm text-green-500">REAL</div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-gray-600">{{ $untestedReports }}</div>
            <div class="text-sm text-gray-500">UNTESTED</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <!-- Biểu đồ theo danh mục -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Báo cáo theo danh mục</h3>
            <div class="space-y-3">
                @foreach($reportsByCategory as $cat)
                <div class="flex items-center gap-3">
                    <div class="w-32 text-sm text-gray-600 truncate">{{ $cat->name }}</div>
                    <div class="flex-1 bg-gray-100 rounded-full h-5">
                        <div class="h-full rounded-full" 
                             style="width: {{ $totalReports > 0 ? ($cat->reports_count / max($totalReports, 1)) * 100 : 0 }}%; background-color: {{ $cat->color_code ?? '#dc2626' }};">
                        </div>
                    </div>
                    <div class="text-sm font-bold text-gray-800 w-8">{{ $cat->reports_count }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Biểu đồ 7 ngày -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Báo cáo 7 ngày qua</h3>
            <div class="flex items-end gap-2 h-40">
                @foreach($reportsByDay as $day)
                @php $maxCount = $reportsByDay->max('count') ?: 1; @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="text-xs font-bold text-gray-800">{{ $day->count }}</div>
                    <div class="w-full bg-red-500 rounded-t" 
                         style="height: {{ ($day->count / $maxCount) * 100 }}%; min-height: 4px;">
                    </div>
                    <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Báo cáo gần đây -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">📋 Báo cáo gần đây</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Người gửi</th>
                        <th class="p-3 text-left">Danh mục</th>
                        <th class="p-3 text-left">AI Label</th>
                        <th class="p-3 text-left">Trạng thái</th>
                        <th class="p-3 text-left">Thời gian</th>
                        <th class="p-3 text-left">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentReports as $report)
                    <tr class="border-t hover:bg-gray-50 {{ $report->ai_label === 'FAKE' ? 'bg-red-50/50' : ($report->ai_label === 'REAL' ? 'bg-green-50/50' : '') }}">
                        <td class="p-3 font-medium">#{{ $report->id }}</td>
                        <td class="p-3">{{ $report->user?->name ?? 'Ẩn danh' }}</td>
                        <td class="p-3">{{ $report->category->name }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $report->ai_label === 'FAKE' ? 'bg-red-100 text-red-700' : 
                                   ($report->ai_label === 'REAL' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $report->ai_label }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($report->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $report->status === 'pending' ? 'Chờ xử lý' : 
                                   ($report->status === 'completed' ? 'Đã duyệt' : 'Từ chối') }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-500">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.reports.show', $report->id) }}" 
                               class="text-blue-600 hover:underline text-xs font-medium">Chi tiết</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection