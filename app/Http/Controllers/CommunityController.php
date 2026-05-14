<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    /**
     * Trang danh sách bài đăng đã duyệt
     */
    public function index()
    {
        $posts = CommunityPost::with(['user', 'media', 'likes', 'comments'])
            ->approved()
            ->latest('approved_at')
            ->paginate(5);

        return view('community.index', compact('posts'));
    }

    /**
     * Chi tiết bài đăng
     */
    public function show(CommunityPost $post)
    {
        // Chỉ hiển thị bài đã duyệt (trừ admin)
        if ($post->status !== 'approved' && (!auth()->check() || auth()->user()->role !== 'admin')) {
            abort(404);
        }

        $post->load(['user', 'media', 'comments.user', 'likes']);

        return view('community.show', compact('post'));
    }

    /**
     * Đăng bài mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:5000',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ]);

        $post = CommunityPost::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'pending',
        ]);

        // Upload media
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $i => $file) {
                if ($i >= 5) break; // Tối đa 5 file
                
                $path = $file->store('community', 'public');
                $type = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
                
                $post->media()->create([
                    'file_path' => $path,
                    'file_type' => $type,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('community.index')
            ->with('success', 'Bài viết đã được gửi, đang chờ admin kiểm duyệt!');
    }

    /**
     * Comment vào bài đăng
     */
    public function comment(Request $request, CommunityPost $post)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('comments', 'public');
            $comment->update(['image_path' => $path]);
        }

        return back()->with('success', 'Bình luận đã được gửi!');
    }

    /**
     * Like/Unlike bài đăng
     */
    public function like(Request $request, CommunityPost $post)
    {
        $userId = auth()->id();
        $existing = $post->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $post->likes()->count(),
        ]);
    }
}