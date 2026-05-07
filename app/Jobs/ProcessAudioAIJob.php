<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\ReportStatusHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessAudioAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $timeout = 120;       // ✅ Thêm type int
    public int $tries = 3;           // ✅ Thêm type int
    
    // ✅ ĐÃ FIX: Thêm type cho property $report
    protected Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function handle(): void      // ✅ Thêm return type
    {
        try {
            // Cập nhật trạng thái đang xử lý
            $this->report->update(['status' => 'processing']);
            
            // Log lịch sử
            ReportStatusHistory::create([
                'report_id' => $this->report->id,
                'changed_by_user_id' => null,
                'old_status' => 'pending',
                'new_status' => 'processing',
                'note' => 'Đang gửi đến AI để phân tích',
                'created_at' => now(),
            ]);

            // Gọi AI Service
            $aiServiceUrl = config('services.ai.url', 'http://localhost:8001');
            $response = Http::timeout(60)->post($aiServiceUrl . '/api/predict', [
                'audio_url' => $this->report->audio_url
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Cập nhật kết quả AI
                $this->report->update([
                    'ai_label' => $result['label'],
                    'ai_confidence' => $result['confidence'],
                    'status' => 'completed',
                ]);
                
                // Log kết quả
                ReportStatusHistory::create([
                    'report_id' => $this->report->id,
                    'changed_by_user_id' => null,
                    'old_status' => 'processing',
                    'new_status' => 'completed',
                    'note' => "Kết quả AI: {$result['label']} (Độ tin cậy: " . ($result['confidence'] * 100) . "%)",
                    'created_at' => now(),
                ]);
                
                Log::info('AI Processing completed', [
                    'report_id' => $this->report->id,
                    'label' => $result['label'],
                    'confidence' => $result['confidence']
                ]);
                
            } else {
                $this->report->update(['status' => 'pending']);
                
                Log::error('AI Service response error', [
                    'report_id' => $this->report->id,
                    'response' => $response->body()
                ]);
                
                if ($this->attempts() < 3) {
                    $this->release(60);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('AI Processing failed', [
                'report_id' => $this->report->id,
                'error' => $e->getMessage()
            ]);
            
            $this->report->update(['status' => 'pending']);
            
            if ($this->attempts() < 3) {
                $this->release(60);
            } else {
                ReportStatusHistory::create([
                    'report_id' => $this->report->id,
                    'changed_by_user_id' => null,
                    'old_status' => 'processing',
                    'new_status' => 'pending',
                    'note' => 'Lỗi xử lý AI: ' . $e->getMessage(),
                    'created_at' => now(),
                ]);
            }
            
            throw $e;
        }
    }
}