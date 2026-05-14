@extends('layouts.admin')

@section('title', 'Kiểm duyệt bài đăng')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">📰 Kiểm duyệt bài đăng cộng đồng</h1>

    <div class="flex gap-2 mb-4">
        <a href="?status=" class="px-4 py-2 rounded-lg text-sm {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-700' }}">Tất cả</a>
        <a href="?status=pending" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700' }}">⏳ Chờ duyệt</a>
        <a href="?status=approved" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-white text-gray-700' }}">✅ Đã duyệt</a>
        <a href="?status=rejected" class="px-4 py-2 rounded-lg text-sm {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-gray-700' }}">❌ Từ chối</a>
    </div>

    <div class="space-y-4">
        @foreach($posts as $post)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="font-bold text-lg">{{ $post->title }}</h3>
                    <p class="text-sm text-gray-500">{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</p>
                    <p class="text-gray-700 mt-2">{{ Str::limit($post->content, 200) }}</p>
                    
                    @if($post->media->count() > 0)
                    <div class="flex gap-2 mt-3 overflow-x-auto">
                        @foreach($post->media as $media)
                            @if($media->file_type === 'image')
                                <img src="{{ $media->url }}" class="h-24 rounded-lg object-cover">
                            @else
                                <video src="{{ $media->url }}" class="h-24 rounded-lg"></video>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                
                @if($post->status === 'pending')
                <div class="flex gap-2 ml-4">
                    <form action="{{ route('admin.community.approve', $post) }}" method="POST">
                        @csrf
                        <button class="bg-green-600 text-white px-3 py-2 rounded-lg text-sm">✅ Duyệt</button>
                    </form>
                    <form action="{{ route('admin.community.reject', $post) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="note" placeholder="Lý do từ chối" class="border rounded-lg px-2 py-1 text-sm w-32">
                        <button class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm">❌ Từ chối</button>
                    </form>
                </div>
                @else
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $post->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $post->status === 'approved' ? 'Đã duyệt' : 'Từ chối' }}
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection