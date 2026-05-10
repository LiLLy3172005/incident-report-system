@extends('layouts.admin')

@section('title', 'Quản lý báo cáo')

@section('content')
<div class="p-6">
   <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📋 Quản lý báo cáo</h1>
    <div class="flex gap-3">
        <span class="text-sm text-gray-500">{{ $reports->total() }} báo cáo</span>
        <a href="{{ route('admin.reports.export') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            📥 Export CSV
        </a>
    </div>
</div>
    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="🔍 ID, địa chỉ, mô tả..." 
                   class="border rounded-lg px-3 py-2 text-sm">

            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
            </select>

            <select name="ai_label" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tất cả AI label</option>
                <option value="FAKE" {{ request('ai_label') === 'FAKE' ? 'selected' : '' }}>FAKE</option>
                <option value="REAL" {{ request('ai_label') === 'REAL' ? 'selected' : '' }}>REAL</option>
                <option value="UNTESTED" {{ request('ai_label') === 'UNTESTED' ? 'selected' : '' }}>UNTESTED</option>
            </select>

            <select name="category_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-red-700">
                Lọc
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left">
                        <a href="?sort=id&dir={{ request('dir') === 'asc' ? 'desc' : 'asc' }}" class="hover:text-red-600">ID ↕</a>
                    </th>
                    <th class="p-3 text-left">Người gửi</th>
                    <th class="p-3 text-left">Danh mục</th>
                    <th class="p-3 text-left">AI Label</th>
                    <th class="p-3 text-left">Trạng thái</th>
                    <th class="p-3 text-left">Địa chỉ</th>
                    <th class="p-3 text-left">Thời gian</th>
                    <th class="p-3 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr class="border-b hover:bg-gray-50 transition
                    {{ $report->ai_label === 'FAKE' ? 'bg-red-50/40' : '' }}
                    {{ $report->ai_label === 'REAL' ? 'bg-green-50/40' : '' }}">
                    <td class="p-3 font-mono font-bold">#{{ $report->id }}</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ $report->user?->name ?? 'Ẩn danh' }}</span>
                            @if($report->user && $report->user->strikes > 0)
                                <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">
                                    ⚠️{{ $report->user->strikes }}
                                </span>
                            @endif
                            @if($report->user && $report->user->is_banned)
                                <span class="text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-full">🚫</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium text-white"
                              style="background-color: {{ $report->category->color_code ?? '#808080' }}">
                            {{ $report->category->name }}
                        </span>
                    </td>
                   <td class="p-3">
    @if($report->ai_label === 'PROCESSING')
        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
            ⏳ ĐANG XỬ LÝ
        </span>
    @elseif($report->ai_label === 'FAKE')
        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-300">
            ❌ FAKE
        </span>
    @elseif($report->ai_label === 'REAL')
        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-300">
            ✅ REAL
        </span>
    @else
        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
            {{ $report->ai_label ?? 'UNTESTED' }}
        </span>
    @endif
    
    @if($report->ai_confidence)
        <span class="text-xs text-gray-400 ml-1">{{ $report->ai_confidence }}%</span>
    @endif
</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold
                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               ($report->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                            {{ $report->status === 'pending' ? 'Chờ' : ($report->status === 'completed' ? 'Duyệt' : 'Từ chối') }}
                        </span>
                    </td>
                    <td class="p-3 text-gray-600 max-w-[200px] truncate">
                        {{ $report->address_text ?? '—' }}
                    </td>
                    <td class="p-3 text-gray-500 text-xs">
                        {{ $report->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="p-3 text-center">
                        <a href="{{ route('admin.reports.show', $report->id) }}" 
                           class="text-blue-600 hover:underline text-xs font-medium">
                            Chi tiết →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-400">Không có báo cáo nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection