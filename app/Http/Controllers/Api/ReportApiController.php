<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\IncidentCategory;
use App\Jobs\ProcessAudioAIJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Validator;

class ReportApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:incident_categories,id',
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a|max:20480',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $audioFile = $request->file('audio');
            
            if (!env('CLOUDINARY_URL')) {
                $audioPath = $audioFile->store('audio', 'public');
                $audioUrl = asset('storage/' . $audioPath);
            } else {
                $uploadResult = Cloudinary::upload($audioFile->getRealPath(), [
                    'folder' => 'incident_reports',
                    'resource_type' => 'auto',
                ]);
                $audioUrl = $uploadResult->getSecurePath();
            }

            $report = Report::create([
                'user_id' => $request->boolean('is_anonymous', false) ? null : Auth::id(),
                'category_id' => $request->category_id,
                'audio_url' => $audioUrl,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address_text' => $request->address_text,
                'description' => $request->description,
                'status' => 'pending',
                'ai_label' => 'UNTESTED',
            ]);

            ProcessAudioAIJob::dispatch($report);

            return response()->json([
                'success' => true,
                'message' => 'Báo cáo đã được gửi thành công!',
                'data' => $report
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userReports(Request $request)
    {
        $reports = Report::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'category_name' => $report->category->name,
                    'category_color' => $report->category->color_code,
                    'status' => $report->status,
                    'status_text' => $this->getStatusText($report->status),
                    'created_at' => $report->created_at->format('d/m/Y H:i'),
                    'address' => $report->address_text,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    // ✅ ĐÃ FIX: Thêm string type cho $id
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $report = Report::with('category', 'statusHistories')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $report->id,
                'category' => $report->category->name,
                'audio_url' => $report->audio_url,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'address' => $report->address_text,
                'description' => $report->description,
                'status' => $report->status,
                'status_text' => $this->getStatusText($report->status),
                'created_at' => $report->created_at->format('d/m/Y H:i'),
                'histories' => $report->statusHistories,
            ]
        ]);
    }

    // ✅ ĐÃ FIX: Thêm string type cho $status
    private function getStatusText(string $status): string
    {
        return match($status) {
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Đã tiếp nhận',
            'rejected' => 'Bị từ chối',
            default => 'Không xác định'
        };
    }
}