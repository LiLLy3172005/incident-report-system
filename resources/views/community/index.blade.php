@extends('layouts.app')

@section('title', 'Cộng đồng')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">👥 Cộng đồng</h1>
                <p class="text-gray-500 text-sm mt-1">Tin tức & sự kiện từ người dân</p>
            </div>
            @auth
            <a href="#postForm" class="bg-red-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-red-700 transition">
                ✏️ Đăng bài
            </a>
            @endauth
        </div>

        <!-- Form đăng bài -->
        @auth
        <div id="postForm" class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h2 class="font-bold text-gray-800 mb-4">Đăng tin mới</h2>
            <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="title" required 
                       class="w-full border rounded-xl px-4 py-3 mb-3 focus:ring-2 focus:ring-red-500"
                       placeholder="Tiêu đề tin...">
                <textarea name="content" rows="4" 
                          class="w-full border rounded-xl px-4 py-3 mb-3 focus:ring-2 focus:ring-red-500"
                          placeholder="Nội dung chi tiết..."></textarea>
                <div class="mb-3">
                    <label class="block text-sm text-gray-500 mb-2">📷 Ảnh/Video đính kèm</label>
                    <input type="file" name="media[]" multiple accept="image/*,video/*" 
                           class="border rounded-lg px-3 py-2 text-sm w-full">
                    <p class="text-xs text-gray-400 mt-1">Tối đa 5 file, mỗi file < 20MB</p>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-xl font-medium hover:bg-red-700 transition">
                    Gửi bài viết
                </button>
            </form>
        </div>
        @endauth

        <!-- Danh sách bài đăng -->
        @forelse($posts as $post)
        <div class="bg-white rounded-2xl shadow-sm mb-4 overflow-hidden">
            <!-- Header post -->
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $post->user->name ?? 'Ẩn danh' }}</p>
                    <p class="text-xs text-gray-400">{{ $post->approved_at?->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Media slider -->
            @if($post->media->count() > 0)
            <div class="relative">
                <div class="flex overflow-x-auto gap-2 px-4 snap-x">
                    @foreach($post->media as $media)
                        @if($media->file_type === 'image')
                            <img src="{{ $media->url }}" class="h-64 rounded-xl object-cover snap-center flex-shrink-0">
                        @else
                            <video controls class="h-64 rounded-xl flex-shrink-0">
                                <source src="{{ $media->url }}">
                            </video>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Content -->
            <div class="p-4">
                <h3 class="font-bold text-lg text-gray-800 mb-2">
                    <a href="{{ route('community.show', $post) }}" class="hover:text-red-600">{{ $post->title }}</a>
                </h3>
                <p class="text-gray-600 text-sm">{{ Str::limit($post->content, 200) }}</p>
            </div>

            <!-- Actions -->
            <div class="px-4 pb-4 flex items-center gap-4 border-t pt-3">
                <button onclick="toggleLike({{ $post->id }})" 
                        class="flex items-center gap-1 text-sm {{ $post->isLikedByUser(auth()->id()) ? 'text-red-600' : 'text-gray-500' }} hover:text-red-600">
                    <span id="likeIcon{{ $post->id }}">{{ $post->isLikedByUser(auth()->id()) ? '❤️' : '🤍' }}</span>
                    <span id="likeCount{{ $post->id }}">{{ $post->likes->count() }}</span>
                </button>
                <a href="{{ route('community.show', $post) }}" class="text-sm text-gray-500 hover:text-blue-600">
                    💬 {{ $post->comments->count() }} bình luận
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <div class="text-5xl mb-4">📭</div>
            <p class="text-gray-500">Chưa có bài đăng nào</p>
        </div>
        @endforelse

        {{ $posts->links() }}
    </div>
</div>

<script>
async function toggleLike(postId) {
    try {
        const res = await fetch(`/community/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        
        document.getElementById(`likeIcon${postId}`).textContent = data.liked ? '❤️' : '🤍';
        document.getElementById(`likeCount${postId}`).textContent = data.count;
    } catch(e) {
        console.error(e);
    }
}
</script>
@endsection