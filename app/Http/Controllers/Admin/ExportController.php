<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class ExportController extends Controller
{
    public function reports()
    {
        $reports = Report::with(['user', 'category'])->latest()->get();
        
        $filename = 'reports_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $output = fopen('php://output', 'w');
        
        // BOM cho UTF-8 để Excel hiển thị tiếng Việt đúng
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, [
            'ID', 'Người gửi', 'SĐT', 'Danh mục', 'AI Label', 
            'Confidence', 'Trạng thái', 'Địa chỉ', 'Mô tả', 'Ngày tạo'
        ]);
        
        // Data
        foreach ($reports as $r) {
            fputcsv($output, [
                $r->id,
                $r->user?->name ?? 'Ẩn danh',
                $r->user?->phone ?? '',
                $r->category->name ?? '',
                $r->ai_label,
                $r->ai_confidence . '%',
                $r->status === 'pending' ? 'Chờ xử lý' : ($r->status === 'completed' ? 'Đã duyệt' : 'Đã từ chối'),
                $r->address_text ?? '',
                $r->description ?? '',
                $r->created_at->format('d/m/Y H:i'),
            ]);
        }
        
        fclose($output);
        
        return response()->stream(
            function () { /* streamed */ },
            200,
            $headers
        );
    }
}