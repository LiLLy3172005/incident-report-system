<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\IncidentCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng quan
        $totalReports = Report::count();
        $todayReports = Report::whereDate('created_at', today())->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $completedReports = Report::where('status', 'completed')->count();
        $rejectedReports = Report::where('status', 'rejected')->count();
        $fakeReports = Report::where('ai_label', 'FAKE')->count();
        $realReports = Report::where('ai_label', 'REAL')->count();
        $untestedReports = Report::where('ai_label', 'UNTESTED')->count();
        $totalUsers = User::where('role', 'user')->count();
        $bannedUsers = User::where('is_banned', true)->count();

        // Báo cáo theo danh mục
        $reportsByCategory = IncidentCategory::withCount('reports')->get();

        // Báo cáo theo ngày (7 ngày gần nhất)
        $reportsByDay = Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top người dùng
        $topUsers = User::where('role', 'user')
            ->withCount('reports')
            ->orderByDesc('reports_count')
            ->take(5)
            ->get();

        // Báo cáo gần đây
        $recentReports = Report::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalReports',
            'todayReports',
            'pendingReports',
            'completedReports',
            'rejectedReports',
            'fakeReports',
            'realReports',
            'untestedReports',
            'totalUsers',
            'bannedUsers',
            'reportsByCategory',
            'reportsByDay',
            'topUsers',
            'recentReports'
        ));
    }
}