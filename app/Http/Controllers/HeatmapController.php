<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('category')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // === MẶC ĐỊNH CHỈ HIỂN THỊ BÁO CÁO ĐÃ DUYỆT ===
        // Nếu không chọn status, mặc định lọc "completed"
        if (!$request->filled('status')) {
            $query->where('status', 'completed');
        } else {
            $query->where('status', $request->status);
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $reports = $query->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'lat' => (float) $report->latitude,
                    'lng' => (float) $report->longitude,
                    'category' => $report->category->name,
                    'color' => $report->category->color_code ?? '#dc2626',
                    'address' => $report->address_text,
                    'status' => $report->status,
                    'date' => $report->created_at->format('d/m/Y'),
                ];
            });

        $categories = \App\Models\IncidentCategory::where('is_active', true)->get();

        // Tổng số đã duyệt
        $totalApproved = Report::whereNotNull('latitude')
            ->where('status', 'completed')
            ->count();

        return view('heatmap', compact('reports', 'categories', 'totalApproved'));
    }
}