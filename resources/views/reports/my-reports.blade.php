@extends('layouts.app')

@section('title', 'Lịch sử báo cáo')

@section('content')
<div class="container mx-auto px-4 py-4">
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
        <span class="text-2xl mr-2">📋</span> Lịch sử báo cáo của tôi
    </h2>
    
    @if($reports->count() > 0)
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="card hover:shadow-xl transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white mr-3"
                                     style="background-color: {{ $report->category->color_code ?? '#dc2626' }}">
                                    🚨
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $report->category->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($report->address_text)
                                <p class="text-sm text-gray-600 mb-2">📍 {{ $report->address_text }}</p>
                            @endif
                            
                            <div class="flex items-center mt-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($report->status === 'completed') bg-green-100 text-green-700
                                    @elseif($report->status === 'rejected') bg-red-100 text-red-700
                                    @elseif($report->status === 'processing') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    @if($report->status === 'pending') ⏳ Đang chờ xử lý
                                    @elseif($report->status === 'processing') 🤖 Đang xử lý
                                    @elseif($report->status === 'completed') ✅ Đã tiếp nhận
                                    @elseif($report->status === 'rejected') ❌ Bị từ chối
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <a href="{{ route('reports.show', $report->id) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            Xem chi tiết →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @else
        <div class="card text-center py-12">
            <div class="text-6xl mb-4">📭</div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có báo cáo nào</h3>
            <p class="text-gray-600 mb-4">Bạn chưa gửi báo cáo sự cố nào.</p>
            <a href="{{ route('reports.create') }}" class="btn-primary inline-block">
                📝 Gửi báo cáo đầu tiên
            </a>
        </div>
    @endif
</div>
@endsection