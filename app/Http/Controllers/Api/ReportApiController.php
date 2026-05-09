<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\IncidentCategory;
use App\Jobs\ProcessAudioAIJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportApiController extends Controller
{
    /**
     * Store a new report via API.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:incident_categories,id',
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a,mp4,ogg|max:20480',
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

            // Upload audio
            if (!env('CLOUDINARY_URL')) {
                $audioPath = $audioFile->store('reports/audio', 'public');
                $audioUrl = asset('storage/' . $audioPath);
            } else {
                $uploadResult = Cloudinary::upload($audioFile->getRealPath(), [
                    'folder' => 'incident_reports',
                    'resource_type' => 'auto',
                ]);
                $audioUrl = $uploadResult->getSecurePath();
            }

            // Tạo report
            $report = Report::create([
                'user_id' => $request->boolean('is_anonymous', false) ? null : Auth::id(),
                'category_id' => $request->category_id,
                'audio_url' => $audioUrl,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address_text' => $request->address_text,
                'description' => $request->description,
                'status' => 'pending',
                'ai_label' => 'PROCESSING', // ← Đổi thành PROCESSING
                'ai_confidence' => null,
            ]);

            // ✅ Dispatch AI Job
            ProcessAudioAIJob::dispatch($report);
            
            Log::info("Report #{$report->id} created via API, AI job dispatched");

            return response()->json([
                'success' => true,
                'message' => 'Báo cáo đã được gửi! AI đang phân tích...',
                'data' => [
                    'id' => $report->id,
                    'status' => $report->status,
                    'ai_label' => $report->ai_label,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Report Store Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reports.
     */
    public function userReports(Request $request): JsonResponse
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
                    'ai_label' => $report->ai_label,
                    'ai_confidence' => $report->ai_confidence,
                    'created_at' => $report->created_at->format('d/m/Y H:i'),
                    'address' => $report->address_text,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Show report detail.
     */
    public function show(string $id): JsonResponse
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
                'ai_label' => $report->ai_label,
                'ai_confidence' => $report->ai_confidence,
                'created_at' => $report->created_at->format('d/m/Y H:i'),
                'histories' => $report->statusHistories,
            ]
        ]);
    }

    /**
     * Get status text in Vietnamese.
     */
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