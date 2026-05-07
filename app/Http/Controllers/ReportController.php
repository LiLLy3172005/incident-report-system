<?php

namespace App\Http\Controllers;

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
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:incident_categories,id',
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a|max:20480',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        try {
            $audioFile = $request->file('audio');
            
            if (env('CLOUDINARY_URL')) {
                $uploadResult = Cloudinary::upload($audioFile->getRealPath(), [
                    'folder' => 'incident_reports',
                    'resource_type' => 'auto',
                ]);
                $audioUrl = $uploadResult->getSecurePath();
            } else {
                $audioPath = $audioFile->store('audio', 'public');
                $audioUrl = asset('storage/' . $audioPath);
            }

            $report = Report::create([
                'user_id' => $request->boolean('is_anonymous') ? null : Auth::id(),
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

            return redirect()->route('reports.my')
                ->with('success', 'Báo cáo đã được gửi thành công! Hệ thống đang xử lý.');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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

    // ✅ ĐÃ FIX: Thêm string type cho $id
    public function show(string $id)
    {
        $report = Report::with('category', 'statusHistories')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('reports.show', compact('report'));
    }
}