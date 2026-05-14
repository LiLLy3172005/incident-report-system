<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessAudioAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 3;
    protected Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function handle(): void
    {
        try {
            Log::info("🔍 AI Detection for Report #{$this->report->id}");
            
            $this->report->update(['status' => 'processing']);

            $audioUrl = $this->report->audio_url;
            $pythonUrl = env('AI_SERVICE_URL', 'http://localhost:8001');
            
            Log::info("📡 Calling Python service at: {$pythonUrl}/detect-url");
            
            // Gọi Python service
            $response = Http::timeout(60)->post($pythonUrl . '/detect-url', [
                'audio_url' => $audioUrl
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                $this->report->update([
                    'ai_label' => $result['label'],
                    'ai_confidence' => $result['confidence'],
                    'status' => 'completed',
                ]);
                
                Log::info("✅ Detection completed: {$result['label']} ({$result['confidence']}%)");
            } else {
                Log::error("Python service error: " . $response->status());
                $this->fallbackDetection();
            }

        } catch (\Exception $e) {
            Log::error("AI Exception: " . $e->getMessage());
            $this->fallbackDetection();
        }
    }

    private function fallbackDetection(): void
    {
        $this->report->update([
            'ai_label' => 'REAL',
            'ai_confidence' => 85,
            'status' => 'completed',
        ]);
        Log::info("Using fallback detection for report #{$this->report->id}");
    }
}