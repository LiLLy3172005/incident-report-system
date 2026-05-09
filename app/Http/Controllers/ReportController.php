<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;  // ← THÊM DÒNG NÀY

use App\Models\Report;
use App\Models\IncidentCategory;
use App\Jobs\ProcessAudioAIJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $categories = IncidentCategory::where('is_active', true)->get();
        
        // Lấy dữ liệu đã lưu trong session (nếu có)
        $reportData = session('report_data', []);
        
        return view('reports.create', compact('categories', 'reportData'));
    }

    public function storeStep1(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:incident_categories,id',
            'description' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        // Lưu step 1 vào session
        session(['report_data.step1' => [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]]);

        return response()->json(['success' => true, 'next_step' => 2]);
    }

    public function storeStep2(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address_text' => 'nullable|string|max:255',
        ]);

        // Lưu step 2 vào session
        $reportData = session('report_data', []);
        $reportData['step2'] = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address_text' => $request->address_text,
        ];
        session(['report_data' => $reportData]);

        return response()->json(['success' => true, 'next_step' => 3]);
    }

    public function storeStep3(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a|max:20480',
        ]);

        // Lưu file audio tạm thời
        $audioFile = $request->file('audio');
        $audioPath = $audioFile->store('temp_audio', 'public');
        
        $reportData = session('report_data', []);
        $reportData['step3'] = [
            'temp_audio_path' => $audioPath,
        ];
        session(['report_data' => $reportData]);

        return response()->json(['success' => true, 'next_step' => 4]);
    }

  public function storeFinal(Request $request)
{
    $reportData = session('report_data', []);
    
    if (!isset($reportData['step1']) || !isset($reportData['step2']) || !isset($reportData['step3'])) {
        return redirect()->route('reports.create')
            ->with('error', 'Dữ liệu không đầy đủ, vui lòng thử lại');
    }

    $step1 = $reportData['step1'];
    $step2 = $reportData['step2'];
    $step3 = $reportData['step3'];

    try {
        $audioPath = storage_path('app/public/' . $step3['temp_audio_path']);
        $audioUrl = asset('storage/' . $step3['temp_audio_path']);

        $report = Report::create([
            'user_id' => $step1['is_anonymous'] ? null : Auth::id(),
            'category_id' => $step1['category_id'],
            'audio_url' => $audioUrl,
            'latitude' => $step2['latitude'],
            'longitude' => $step2['longitude'],
            'address_text' => $step2['address_text'],
            'description' => $step1['description'],
            'status' => 'pending',
            'ai_label' => 'PROCESSING', // ← Đánh dấu đang xử lý
            'ai_confidence' => null,
        ]);

        // ✅ Dispatch job xử lý AI
        ProcessAudioAIJob::dispatch($report);

        session()->forget('report_data');

        return redirect()->route('reports.my')
            ->with('success', 'Báo cáo đã được gửi! Hệ thống AI đang phân tích giọng nói...');

    } catch (\Exception $e) {
        Log::error('Store report error: ' . $e->getMessage());
        return redirect()->route('reports.create')
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}

    public function userReports()
    {
        $reports = Report::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.my-reports', compact('reports'));
    }

    public function show(string $id)
    {
        $report = Report::with('category', 'statusHistories')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('reports.show', compact('report'));
    }




    // app/Http/Controllers/ReportController.php

// Thêm sau method create()

public function store(Request $request)
{
    $request->validate([
        'category_id'  => 'required|exists:incident_categories,id',
        'description'  => 'nullable|string|max:1000',
        'is_anonymous' => 'nullable',
        'latitude'     => 'nullable|numeric|between:-90,90',
        'longitude'    => 'nullable|numeric|between:-180,180',
        'address_text' => 'nullable|string|max:255',
        // ✅ Thêm mp4, ogg vì browser có thể record các định dạng khác nhau
        'audio'        => 'required|file|mimes:webm,wav,mp3,m4a,mp4,ogg|max:20480',
    ]);

    try {
        $audioFile = $request->file('audio');

        if (!config('services.cloudinary.url') && !env('CLOUDINARY_URL')) {
            $path = $audioFile->store('reports/audio', 'public');
            $audioUrl = asset('storage/' . $path);
        } else {
            $uploadResult = Cloudinary::upload($audioFile->getRealPath(), [
                'folder'        => 'incident_reports',
                'resource_type' => 'auto',
            ]);
            $audioUrl = $uploadResult->getSecurePath();
        }

        $isAnonymous = filter_var($request->input('is_anonymous'), FILTER_VALIDATE_BOOLEAN);

        $report = Report::create([
            'user_id'      => $isAnonymous ? null : Auth::id(),
            'category_id'  => $request->category_id,
            'description'  => $request->description,
            'audio_url'    => $audioUrl,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'address_text' => $request->address_text,
            'status'       => 'pending',
            'ai_label'     => 'UNTESTED',
        ]);

        ProcessAudioAIJob::dispatch($report);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được gửi thành công!',
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
        ], 500);
    }
}
}