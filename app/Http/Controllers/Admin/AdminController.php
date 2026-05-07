<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\IncidentCategory;
use App\Models\ReportStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_reports_today' => Report::whereDate('created_at', today())->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'processing_reports' => Report::where('status', 'processing')->count(),
            'fake_reports' => Report::where('ai_label', 'FAKE')->count(),
            'real_reports' => Report::where('ai_label', 'REAL')->count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'total_users' => User::count(),
        ];

        // Thống kê theo ngày
        $reports_by_date = Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Thống kê theo category
        $reports_by_category = IncidentCategory::withCount('reports')
            ->having('reports_count', '>', 0)
            ->get();

        return view('admin.dashboard', compact('stats', 'reports_by_date', 'reports_by_category'));
    }

    public function index(Request $request)
    {
        $query = Report::with('user', 'category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ai_label')) {
            $query->where('ai_label', $request->ai_label);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(string $id)  // ← THÊM TYPE HINT
    {
        $report = Report::with('user', 'category', 'statusHistories.changedByUser')
            ->findOrFail($id);

        return view('admin.reports.show', compact('report'));
    }

    public function approve(string $id)  // ← THÊM TYPE HINT
    {
        $report = Report::findOrFail($id);
        
        $oldStatus = $report->status;
        $report->update(['status' => 'completed']);
        
        ReportStatusHistory::create([
            'report_id' => $report->id,
            'changed_by_user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'completed',
            'note' => 'Admin đã duyệt báo cáo này',
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã duyệt báo cáo thành công!');
    }

    public function reject(string $id)  // ← THÊM TYPE HINT
    {
        $report = Report::findOrFail($id);
        
        $oldStatus = $report->status;
        $report->update([
            'status' => 'rejected',
            'ai_label' => 'FAKE'
        ]);
        
        // Cộng 1 strike cho user
        if ($report->user_id) {
            $user = User::find($report->user_id);
            $user->addStrike();
        }
        
        ReportStatusHistory::create([
            'report_id' => $report->id,
            'changed_by_user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'note' => 'Admin đánh dấu là tin giả',
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã từ chối báo cáo!');
    }
}