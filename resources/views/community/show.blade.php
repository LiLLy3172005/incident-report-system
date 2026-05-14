@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Nội dung bài đăng -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <a href="{{ route('community.index') }}" class="text-gray-500 text-sm mb-4 inline-block">← Quay lại</a>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $post->title }}</h1>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium">{{ $post->user->name ?? 'Ẩn danh' }}</p>
                    <p class="text-xs text-gray-400">{{ $post->approved_at?->diffForHumans() }}</p>
                </div>
            </div>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
        </div>

        <!-- Comment Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4">💬 Bình luận ({{ $post->comments->count() }})</h3>
            
            @auth
            <!-- Form comment -->
            <form action="{{ route('community.comment', $post) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                @csrf
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full bg-gray-300 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <textarea name="content" rows="2" 
                                  class="w-full border rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-500"
                                  placeholder="Viết bình luận..."></textarea>
                        <div class="flex gap-2 mt-2">
                            <input type="file" name="image" accept="image/*" class="text-xs">
                            <label class="flex items-center gap-1 text-xs text-gray-500">
                                <input type="checkbox" name="is_anonymous" value="1"> Ẩn danh
                            </label>
                            <button type="submit" class="ml-auto bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-red-700">
                                Gửi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endauth

            <!-- Danh sách comment -->
            <div class="space-y-4">
                @foreach($post->comments as $comment)
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($comment->is_anonymous ? 'A' : ($comment->user->name ?? 'A'), 0, 1)) }}
                    </div>
                    <div class="flex-1 bg-gray-50 rounded-xl p-3">
                        <p class="text-xs font-medium text-gray-700">
                            {{ $comment->is_anonymous ? 'Ẩn danh' : ($comment->user->name ?? 'Đã xóa') }}
                            <span class="text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                        </p>
                        @if($comment->content)
                            <p class="text-sm text-gray-600 mt-1">{{ $comment->content }}</p>
                        @endif
                        @if($comment->image_url)
                            <img src="{{ $comment->image_url }}" class="mt-2 rounded-lg max-h-48 object-cover">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection