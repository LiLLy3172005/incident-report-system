<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\IncidentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request): View
    {
        $query = Report::with(['user', 'category']);

        // Filter theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo AI label
        if ($request->filled('ai_label')) {
            $query->where('ai_label', $request->ai_label);
        }

        // Filter theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search theo ID hoặc địa chỉ
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('address_text', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Dùng appends() thay vì withQueryString()
        $reports = $query->paginate(15)->appends($request->all());
        $categories = IncidentCategory::where('is_active', true)->get();

        return view('admin.reports.index', compact('reports', 'categories'));
    }

    /**
     * Display the specified report.
     */
    public function show(int $id): View
    {
        $report = Report::with(['user', 'category', 'statusHistories.changedBy'])
            ->findOrFail($id);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Approve a report.
     */
    public function approve(int $id, Request $request): RedirectResponse
    {
        $report = Report::findOrFail($id);
        $oldStatus = $report->status;

        $report->update(['status' => 'completed']);

        // Ghi log lịch sử
        $report->statusHistories()->create([
            'changed_by_user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => 'completed',
            'note' => $request->note ?? 'Admin phê duyệt',
        ]);

        return back()->with('success', 'Đã duyệt báo cáo #' . $report->id);
    }

    /**
     * Reject a report.
     */
    public function reject(int $id, Request $request): RedirectResponse
    {
        $report = Report::findOrFail($id);
        $oldStatus = $report->status;

        $report->update(['status' => 'rejected']);

        // Ghi log lịch sử
        $report->statusHistories()->create([
            'changed_by_user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'note' => $request->note ?? 'Admin từ chối',
        ]);

        // === STRIKE SYSTEM: Cộng strike cho user ===
        if ($report->user_id) {
            $user = $report->user;
            $user->increment('strikes');

            // === BAN USER: Khóa nếu strike >= 3 ===
            if ($user->strikes >= 3) {
                $user->update(['is_banned' => true]);
            }
        }

        return back()->with('success', 'Đã từ chối báo cáo #' . $report->id);
    }
}