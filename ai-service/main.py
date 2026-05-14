# main.py - Wav2Vec2 Deepfake Detection API
import sys
import os
import torch
import librosa
import numpy as np
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse
from transformers import AutoModelForAudioClassification, AutoFeatureExtractor
import uvicorn
import tempfile
import requests
from pydantic import BaseModel

app = FastAPI(title="Audio Deepfake Detection API - Wav2Vec2")

# Cấu hình thiết bị
device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
print(f"📱 Using device: {device}")

# Model configuration
MODEL_NAME = "garystafford/wav2vec2-deepfake-voice-detector"

# Global variables
model = None
feature_extractor = None

class AudioUrlRequest(BaseModel):
    audio_url: str

def load_model():
    """Load Wav2Vec2 model for deepfake detection"""
    global model, feature_extractor
    
    try:
        print(f"🔄 Loading model: {MODEL_NAME}")
        model = AutoModelForAudioClassification.from_pretrained(MODEL_NAME)
        feature_extractor = AutoFeatureExtractor.from_pretrained(MODEL_NAME)
        
        model.to(device)
        model.eval()
        
        print(f"✅ Model loaded successfully on {device}")
        print(f"📊 Model parameters: {sum(p.numel() for p in model.parameters()):,}")
        return True
    except Exception as e:
        print(f"❌ Error loading model: {e}")
        return False

def detect_deepfake_from_audio(audio_path):
    """Dự đoán REAL/FAKE từ file audio"""
    global model, feature_extractor
    
    if model is None:
        return {"label": "REAL", "confidence": 50.0, "error": "Model not loaded"}
    
    try:
        # Load và preprocess audio (16kHz, mono)
        audio, sr = librosa.load(audio_path, sr=16000, mono=True)
        
        # Giới hạn độ dài (tối ưu: 2.5-13 giây)
        max_duration = 13  # giây
        max_samples = max_duration * 16000
        if len(audio) > max_samples:
            audio = audio[:max_samples]
        
        # Extract features
        inputs = feature_extractor(
            audio, 
            sampling_rate=16000, 
            return_tensors="pt", 
            padding=True,
            max_length=max_samples,
            truncation=True
        )
        inputs = {k: v.to(device) for k, v in inputs.items()}
        
        # Predict
        with torch.no_grad():
            outputs = model(**inputs)
            logits = outputs.logits
            probs = torch.nn.functional.softmax(logits, dim=-1)
        
        # Class 0: REAL, Class 1: FAKE
        real_prob = float(probs[0][0].cpu().numpy())
        fake_prob = float(probs[0][1].cpu().numpy())
        
        is_fake = fake_prob > 0.5
        confidence = max(real_prob, fake_prob) * 100
        
        # Log chi tiết
        print(f"🎯 Detection: REAL={real_prob:.4f}, FAKE={fake_prob:.4f}")
        print(f"📊 Decision: {'FAKE' if is_fake else 'REAL'} with {confidence:.1f}% confidence")
        
        return {
            "label": "FAKE" if is_fake else "REAL",
            "confidence": round(confidence, 2),
            "raw_scores": {
                "real": round(real_prob, 4),
                "fake": round(fake_prob, 4)
            }
        }
        
    except Exception as e:
        print(f"❌ Detection error: {e}")
        return {"label": "REAL", "confidence": 50.0, "error": str(e)}

def download_audio_from_url(url, save_path):
    """Tải audio từ URL về local"""
    try:
        # Hỗ trợ cả URL trực tiếp và Google Drive
        if "drive.google.com" in url:
            # Xử lý Google Drive link
            import gdown
            gdown.download(url, save_path, quiet=False)
        else:
            # Download thông thường
            response = requests.get(url, timeout=30)
            response.raise_for_status()
            with open(save_path, 'wb') as f:
                f.write(response.content)
        return True
    except Exception as e:
        print(f"❌ Download error: {e}")
        return False

@app.on_event("startup")
async def startup_event():
    """Khởi tạo model khi service start"""
    success = load_model()
    if not success:
        print("⚠️ Warning: Model failed to load. Service running in limited mode.")

@app.get("/")
async def root():
    return {
        "service": "Audio Deepfake Detection API",
        "model": MODEL_NAME,
        "status": "running",
        "model_loaded": model is not None,
        "device": str(device)
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy", 
        "model_loaded": model is not None,
        "device": str(device)
    }

@app.post("/detect")
async def detect_deepfake(audio: UploadFile = File(...)):
    """Phát hiện deepfake từ file upload"""
    if model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")
    
    try:
        # Lưu file tạm
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp_file:
            content = await audio.read()
            tmp_file.write(content)
            tmp_path = tmp_file.name
        
        # Dự đoán
        result = detect_deepfake_from_audio(tmp_path)
        
        # Xóa file tạm
        os.unlink(tmp_path)
        
        if "error" in result:
            raise HTTPException(status_code=500, detail=result["error"])
        
        return JSONResponse({
            "success": True,
            "label": result["label"],
            "confidence": result["confidence"],
            "raw_scores": result.get("raw_scores", {})
        })
        
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/detect-url")
async def detect_deepfake_from_url(request: AudioUrlRequest):
    """Phát hiện deepfake từ URL audio"""
    if model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")
    
    try:
        # Tạo file tạm
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp_file:
            tmp_path = tmp_file.name
        
        # Tải audio từ URL
        success = download_audio_from_url(request.audio_url, tmp_path)
        if not success:
            raise HTTPException(status_code=400, detail="Failed to download audio from URL")
        
        # Dự đoán
        result = detect_deepfake_from_audio(tmp_path)
        
        # Xóa file tạm
        os.unlink(tmp_path)
        
        if "error" in result:
            raise HTTPException(status_code=500, detail=result["error"])
        
        return JSONResponse({
            "success": True,
            "label": result["label"],
            "confidence": result["confidence"],
            "raw_scores": result.get("raw_scores", {})
        })
        
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/detect-batch")
async def detect_batch(files: list[UploadFile] = File(...)):
    """Phát hiện batch nhiều file"""
    if model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")
    
    results = []
    for audio in files:
        try:
            with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp_file:
                content = await audio.read()
                tmp_file.write(content)
                tmp_path = tmp_file.name
            
            result = detect_deepfake_from_audio(tmp_path)
            os.unlink(tmp_path)
            
            results.append({
                "filename": audio.filename,
                "label": result["label"],
                "confidence": result["confidence"]
            })
        except Exception as e:
            results.append({
                "filename": audio.filename,
                "error": str(e)
            })
    
    return JSONResponse({
        "success": True,
        "total": len(files),
        "results": results
    })

if __name__ == "__main__":
    uvicorn.run(
        "main:app", 
        host="0.0.0.0", 
        port=8001, 
        reload=True,
        log_level="info"
    )