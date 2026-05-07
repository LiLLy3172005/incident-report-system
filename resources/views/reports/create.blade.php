@extends('layouts.app')

@section('title', 'Gửi báo cáo sự cố')

@section('content')
<div x-data="reportApp()" class="container mx-auto px-4 py-4 max-w-md md:max-w-2xl">
    <div class="card">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="text-2xl mr-2">📢</span> Gửi báo cáo sự cố
        </h2>
        
        <form @submit.prevent="submitReport">
            @csrf
            
            <!-- Loại sự cố -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">📍 Loại sự cố</label>
                <select x-model="categoryId" class="input-field" required>
                    <option value="">Chọn loại sự cố</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" style="color: {{ $category->color_code }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Vị trí -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">📍 Vị trí</label>
                <div id="map" class="w-full h-64 rounded-lg border-2 border-gray-300 mb-2"></div>
                <button type="button" @click="getCurrentLocation" class="text-blue-600 text-sm">
                    🔄 Lấy lại vị trí hiện tại
                </button>
                <input type="hidden" x-model="latitude">
                <input type="hidden" x-model="longitude">
                <input type="text" x-model="addressText" placeholder="Hoặc nhập địa chỉ" class="input-field mt-2">
            </div>
            
            <!-- Ghi âm -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">🎙️ Ghi âm mô tả</label>
                
                <!-- Chưa ghi âm -->
                <template x-if="!isRecording && !audioUrl">
                    <button type="button" @click="startRecording" 
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-4 rounded-xl font-bold text-lg hover:from-red-600 transition">
                        🎤 Bắt đầu ghi âm
                    </button>
                </template>
                
                <!-- Đang ghi âm -->
                <template x-if="isRecording">
                    <div class="text-center space-y-3">
                        <div class="flex justify-center">
                            <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center recording-wave">
                                <div class="w-12 h-12 bg-white rounded-full"></div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">Đang ghi... ⏱️ <span x-text="recordingTime"></span> giây</p>
                        <button type="button" @click="stopRecording" class="bg-yellow-500 text-white px-6 py-2 rounded-full">
                            ⏹️ Dừng
                        </button>
                    </div>
                </template>
                
                <!-- Đã ghi xong -->
                <template x-if="audioUrl && !isRecording">
                    <div class="space-y-3">
                        <audio controls class="w-full" x-bind:src="audioUrl"></audio>
                        <div class="flex gap-2">
                            <button type="button" @click="resetRecording" class="flex-1 bg-gray-500 text-white py-2 rounded-lg">
                                🗑️ Ghi lại
                            </button>
                            <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg font-semibold">
                                ✅ Gửi báo cáo
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- Mô tả thêm -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-semibold">📝 Mô tả thêm</label>
                <textarea x-model="description" rows="3" placeholder="Mô tả chi tiết về sự việc..." 
                          class="input-field"></textarea>
            </div>
            
            <!-- Ẩn danh -->
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" x-model="isAnonymous" class="mr-2">
                    <span class="text-gray-700">🙈 Gửi báo cáo ẩn danh</span>
                </label>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function reportApp() {
    return {
        categoryId: '',
        latitude: null,
        longitude: null,
        addressText: '',
        description: '',
        isAnonymous: false,
        isRecording: false,
        audioUrl: null,
        audioBlob: null,
        mediaRecorder: null,
        recordingTime: 0,
        timer: null,
        map: null,
        marker: null,
        
        init() {
            this.getCurrentLocation();
        },
        
        getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    this.latitude = pos.coords.latitude;
                    this.longitude = pos.coords.longitude;
                    this.initMap();
                }, err => {
                    Swal.fire('Lỗi', 'Không thể lấy vị trí: ' + err.message, 'error');
                });
            }
        },
        
        initMap() {
            if (!this.latitude || !this.longitude) return;
            
            const position = { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) };
            
            if (this.map) {
                this.map.setCenter(position);
                this.marker.setPosition(position);
            } else {
                this.map = new google.maps.Map(document.getElementById('map'), {
                    center: position,
                    zoom: 15,
                    zoomControl: true,
                });
                
                this.marker = new google.maps.Marker({
                    position: position,
                    map: this.map,
                    animation: google.maps.Animation.BOUNCE,
                    draggable: true,
                });
                
                google.maps.event.addListener(this.marker, 'dragend', (event) => {
                    this.latitude = event.latLng.lat();
                    this.longitude = event.latLng.lng();
                    this.reverseGeocode();
                });
            }
            
            this.reverseGeocode();
        },
        
        reverseGeocode() {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                location: { lat: parseFloat(this.latitude), lng: parseFloat(this.longitude) }
            }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    this.addressText = results[0].formatted_address;
                }
            });
        },
        
        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                let chunks = [];
                
                this.mediaRecorder.ondataavailable = e => chunks.push(e.data);
                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(chunks, { type: 'audio/webm' });
                    this.audioUrl = URL.createObjectURL(this.audioBlob);
                    this.isRecording = false;
                    clearInterval(this.timer);
                };
                
                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingTime = 0;
                this.timer = setInterval(() => this.recordingTime++, 1000);
                
            } catch(err) {
                Swal.fire('Lỗi', 'Không thể truy cập micro: ' + err.message, 'error');
            }
        },
        
        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        },
        
        resetRecording() {
            this.audioUrl = null;
            this.audioBlob = null;
            this.startRecording();
        },
        
        async submitReport() {
            if (!this.categoryId) {
                Swal.fire('Thiếu thông tin', 'Vui lòng chọn loại sự cố', 'warning');
                return;
            }
            
            if (!this.audioBlob) {
                Swal.fire('Thiếu ghi âm', 'Vui lòng ghi âm mô tả sự cố', 'warning');
                return;
            }
            
            const formData = new FormData();
            formData.append('category_id', this.categoryId);
            formData.append('audio', this.audioBlob, 'recording.webm');
            formData.append('latitude', this.latitude);
            formData.append('longitude', this.longitude);
            formData.append('address_text', this.addressText);
            formData.append('description', this.description);
            formData.append('is_anonymous', this.isAnonymous);
            
            Swal.fire({
                title: 'Đang gửi...',
                text: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const response = await axios.post('/api/reports', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.data.message,
                    confirmButtonColor: '#dc2626'
                }).then(() => {
                    window.location.href = '/my-reports';
                });
                
            } catch(error) {
                Swal.fire('Lỗi', error.response?.data?.message || 'Không thể gửi báo cáo', 'error');
            }
        }
    }
}
</script>
@endpush
@endsection