<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Danh sách bài đăng chờ kiểm duyệt
     */
    public function index(Request $request)
    {
        $query = CommunityPost::with(['user', 'media']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->latest()->paginate(15);

        return view('admin.community.index', compact('posts'));
    }

    /**
     * Duyệt bài đăng
     */
    public function approve(CommunityPost $post)
    {
        $post->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Đã duyệt bài đăng: ' . $post->title);
    }

    /**
     * Từ chối bài đăng
     */
    public function reject(Request $request, CommunityPost $post)
    {
        $post->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_note' => $request->note,
        ]);

        return back()->with('success', 'Đã từ chối bài đăng');
    }

    /**
     * Xóa bài đăng
     */
    public function destroy(CommunityPost $post)
    {
        $post->delete();
        return back()->with('success', 'Đã xóa bài đăng');
    }
}