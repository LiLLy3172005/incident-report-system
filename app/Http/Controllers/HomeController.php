<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\IncidentCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Thống kê nhanh cho trang chủ
        $stats = [
            'total_reports' => Report::count(),
            'today_reports' => Report::whereDate('created_at', today())->count(),
            'resolved_reports' => Report::where('status', 'completed')->count(),
            'categories' => IncidentCategory::count(),
        ];
        
        // Lấy 5 báo cáo mới nhất (công khai)
        $recentReports = Report::with('category')
            ->where('status', 'completed')
            ->where('ai_label', 'REAL')
            ->latest()
            ->take(5)
            ->get();
        
        $categories = IncidentCategory::where('is_active', true)->get();
        
        return view('home', compact('stats', 'recentReports', 'categories'));
    }
}