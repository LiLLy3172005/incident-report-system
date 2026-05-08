@extends('layouts.app')
@section('title', 'Gửi báo cáo sự cố')
@section('content')
<style>
    .step-circle { transition: all 0.3s ease; }
    .step-active { background-color: #dc2626; color: white; border-color: #dc2626; }
    .step-completed { background-color: #22c55e; color: white; border-color: #22c55e; }
    .step-pending { background-color: #f3f4f6; color: #9ca3af; border-color: #e5e7eb; }
    .form-step { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .radio-card { transition: all 0.3s ease; cursor: pointer; }
    .radio-card.active { border-color: #dc2626; background-color: #fef2f2; box-shadow: 0 4px 12px rgba(220,38,38,0.15); }
    .recording-wave { animation: pulse 1.5s ease-in-out infinite; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity:1; } 50% { transform: scale(1.1); opacity:0.7; } }
</style>

{{-- ✅ FIX 1: Thêm x-data vào đây --}}
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4" x-data="reportApp()">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🚨</div>
            <h1 class="text-3xl font-bold text-gray-800">Gửi báo cáo sự cố</h1>
            <p class="text-gray-500 mt-2">Điền thông tin theo từng bước</p>
        </div>

        <!-- Step Indicators -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 text-center">
                    <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold mx-auto mb-2"
                         :class="currentStep >= 1 ? (currentStep > 1 ? 'step-completed' : 'step-active') : 'step-pending'">
                        <span x-show="currentStep > 1">✓</span>
                        <span x-show="currentStep <= 1">1</span>
                    </div>
                    <span class="text-xs text-gray-500">Thông tin</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2">
                    <div class="h-full bg-red-600 transition-all duration-300" :style="'width: ' + (currentStep > 1 ? '100%' : '0%')"></div>
                </div>
                <div class="flex-1 text-center">
                    <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold mx-auto mb-2"
                         :class="currentStep >= 2 ? (currentStep > 2 ? 'step-completed' : 'step-active') : 'step-pending'">
                        <span x-show="currentStep > 2">✓</span>
                        <span x-show="currentStep <= 2">2</span>
                    </div>
                    <span class="text-xs text-gray-500">Vị trí</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2">
                    <div class="h-full bg-red-600 transition-all duration-300" :style="'width: ' + (currentStep > 2 ? '100%' : '0%')"></div>
                </div>
                <div class="flex-1 text-center">
                    <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold mx-auto mb-2"
                         :class="currentStep >= 3 ? 'step-active' : 'step-pending'">3</div>
                    <span class="text-xs text-gray-500">Ghi âm</span>
                </div>
            </div>
        </div>

        <!-- STEP 1 -->
        <div x-show="currentStep === 1" class="form-step">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">📋 Bước 1: Thông tin cơ bản</h3>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-3">Loại sự cố <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($categories as $category)
                        <div class="radio-card border-2 rounded-xl p-4 text-center cursor-pointer"
                             :class="formData.category_id == '{{ $category->id }}' ? 'active border-red-600 bg-red-50' : 'border-gray-200'"
                             @click="formData.category_id = '{{ $category->id }}'">
                            <div class="text-3xl mb-2">
                                @if($category->name == 'Cháy nổ') 🔥
                                @elseif($category->name == 'Tai nạn giao thông') 🚗
                                @elseif($category->name == 'Trộm cắp') 👮
                                @elseif($category->name == 'Cây đổ') 🌳
                                @elseif($category->name == 'Ngập lụt') 💧
                                @else 📌
                                @endif
                            </div>
                            <div class="font-medium text-gray-800">{{ $category->name }}</div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-red-500 text-sm mt-2" x-show="errors.category_id" x-text="errors.category_id"></p>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Mô tả chi tiết</label>
                    <textarea x-model="formData.description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Mô tả chi tiết về sự việc..."></textarea>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <label class="font-semibold text-gray-800">Gửi báo cáo ẩn danh</label>
                        <p class="text-sm text-gray-500">Thông tin của bạn sẽ được bảo mật</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="formData.is_anonymous" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" @click="nextStep" class="px-8 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition">
                    Tiếp theo →
                </button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div x-show="currentStep === 2" class="form-step">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">📍 Bước 2: Xác định vị trí</h3>
                <div class="mb-4">
                    {{-- ✅ FIX 2: map cần min-height kể cả khi đang load --}}
                    <div id="map" style="min-height: 320px;" class="w-full rounded-xl border-2 border-gray-200 mb-3 bg-gray-100 flex items-center justify-center">
                        <span class="text-gray-400 text-sm" x-show="!formData.latitude">Đang tải bản đồ...</span>
                    </div>
                    <button type="button" @click="getCurrentLocation" class="text-red-600 text-sm font-semibold">
                        🔄 Lấy lại vị trí hiện tại
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Địa chỉ</label>
                    <input type="text" x-model="formData.address_text"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500"
                           placeholder="Hoặc nhập địa chỉ thủ công">
                    <p class="text-red-500 text-sm mt-2" x-show="errors.address_text" x-text="errors.address_text"></p>
                </div>
                <input type="hidden" x-model="formData.latitude">
                <input type="hidden" x-model="formData.longitude">
            </div>
            <div class="flex justify-between mt-6">
                <button type="button" @click="prevStep" class="px-8 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    ← Quay lại
                </button>
                <button type="button" @click="nextStep" class="px-8 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition">
                    Tiếp theo →
                </button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div x-show="currentStep === 3" class="form-step">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">🎙️ Bước 3: Ghi âm mô tả</h3>

                {{-- ✅ FIX 3: Hiển thị cảnh báo nếu không phải HTTPS --}}
                <div x-show="!isSecureContext" class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded-xl text-yellow-700 text-sm">
                    ⚠️ Ghi âm yêu cầu kết nối <strong>HTTPS</strong>. Nếu bạn đang chạy trên HTTP, tính năng này sẽ không hoạt động.
                </div>

                <div class="text-center py-8">
                    <template x-if="!isRecording && !audioUrl">
                        <div>
                            <button type="button" @click="startRecording"
                                    class="w-32 h-32 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-4xl transition mx-auto shadow-lg">
                                🎤
                            </button>
                            <p class="text-gray-500 text-sm mt-4">Nhấn để bắt đầu ghi âm</p>
                        </div>
                    </template>

                    <template x-if="isRecording">
                        <div class="text-center">
                            <div class="w-32 h-32 bg-red-500 rounded-full flex items-center justify-center mx-auto recording-wave">
                                <div class="w-16 h-16 bg-white rounded-full"></div>
                            </div>
                            <p class="text-2xl font-bold text-gray-800 mt-4"><span x-text="recordingTime"></span> giây</p>
                            <button type="button" @click="stopRecording" class="mt-4 px-6 py-2 bg-yellow-500 text-white rounded-full font-semibold">
                                ⏹️ Dừng ghi âm
                            </button>
                        </div>
                    </template>

                    <template x-if="audioUrl && !isRecording">
                        <div class="space-y-4">
                            <audio controls class="w-full" :src="audioUrl"></audio>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="resetRecording" class="px-6 py-2 bg-gray-500 text-white rounded-full font-semibold">
                                    🗑️ Ghi lại
                                </button>
                            </div>
                        </div>
                    </template>

                    <p class="text-red-500 text-sm mt-4" x-show="errors.audio" x-text="errors.audio"></p>
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <button type="button" @click="prevStep" class="px-8 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    ← Quay lại
                </button>
                <button type="button" @click="submitReport"
                        :disabled="isSubmitting"
                        class="px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition disabled:opacity-50">
                    <span x-show="!isSubmitting">✅ Gửi báo cáo</span>
                    <span x-show="isSubmitting">Đang gửi...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reportApp() {
    return {
        currentStep: 1,
        isSubmitting: false,
        // ✅ FIX 4: Kiểm tra HTTPS cho ghi âm
        isSecureContext: window.isSecureContext,
        formData: {
            category_id: '',
            description: '',
            is_anonymous: false,
            latitude: null,
            longitude: null,
            address_text: '',
        },
        errors: {},
        isRecording: false,
        audioUrl: null,
        audioBlob: null,
        mediaRecorder: null,
        recordingTime: 0,
        timer: null,
        map: null,
        marker: null,

        // ✅ FIX 5: init() KHÔNG gọi getCurrentLocation ngay
        // Map chỉ init khi user vào step 2
        init() {},

        async nextStep() {
            this.errors = {};
            if (this.currentStep === 1) {
                if (!this.formData.category_id) {
                    this.errors.category_id = 'Vui lòng chọn loại sự cố';
                    return;
                }
                this.currentStep = 2;
                // ✅ FIX 6: Đợi DOM render xong mới init map
                this.$nextTick(() => {
                    this.getCurrentLocation();
                });
            } else if (this.currentStep === 2) {
                if (!this.formData.latitude || !this.formData.longitude) {
                    if (!this.formData.address_text) {
                        this.errors.address_text = 'Vui lòng chọn vị trí trên bản đồ hoặc nhập địa chỉ';
                        return;
                    }
                }
                this.currentStep = 3;
            }
        },

        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },

        async submitReport() {
            if (!this.audioBlob) {
                this.errors.audio = 'Vui lòng ghi âm mô tả sự cố';
                return;
            }
            this.isSubmitting = true;

            const formData = new FormData();
            formData.append('category_id', this.formData.category_id);
            formData.append('description', this.formData.description);
            formData.append('is_anonymous', this.formData.is_anonymous ? 1 : 0);
            formData.append('latitude', this.formData.latitude);
            formData.append('longitude', this.formData.longitude);
            formData.append('address_text', this.formData.address_text);
            // ✅ FIX 7: Gửi đúng MIME type - dùng webm hoặc fallback
            const mimeType = this.audioBlob.type || 'audio/webm';
            const ext = mimeType.includes('mp4') ? 'mp4' : (mimeType.includes('ogg') ? 'ogg' : 'webm');
            formData.append('audio', this.audioBlob, `recording.${ext}`);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            try {
                const response = await axios.post('{{ route("reports.store") }}', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.data.message || 'Báo cáo đã được gửi!',
                    confirmButtonColor: '#dc2626'
                }).then(() => {
                    window.location.href = '{{ route("reports.my") }}';
                });
            } catch (error) {
                let msg = 'Không thể gửi báo cáo';
                if (error.response?.data?.message) msg = error.response.data.message;
                else if (error.response?.data?.errors) msg = Object.values(error.response.data.errors).flat().join(', ');
                Swal.fire('Lỗi', msg, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        getCurrentLocation() {
            if (!navigator.geolocation) {
                Swal.fire('Lỗi', 'Trình duyệt không hỗ trợ định vị', 'error');
                return;
            }
            Swal.fire({
                title: 'Đang lấy vị trí...',
                text: 'Vui lòng cho phép truy cập vị trí',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            navigator.geolocation.getCurrentPosition(
                pos => {
                    this.formData.latitude = pos.coords.latitude;
                    this.formData.longitude = pos.coords.longitude;
                    // ✅ FIX 8: Đảm bảo div map đã có kích thước trước khi init
                    this.$nextTick(() => {
                        this.initMap();
                        Swal.close();
                    });
                },
                err => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Không lấy được vị trí',
                        text: 'Vui lòng nhập địa chỉ thủ công: ' + err.message,
                    });
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        },

        initMap() {
            const mapEl = document.getElementById('map');
            if (!mapEl || !this.formData.latitude || !this.formData.longitude) return;

            const position = {
                lat: parseFloat(this.formData.latitude),
                lng: parseFloat(this.formData.longitude)
            };

            if (this.map) {
                this.map.setCenter(position);
                this.marker.setPosition(position);
            } else {
                this.map = new google.maps.Map(mapEl, {
                    center: position,
                    zoom: 15,
                });
                this.marker = new google.maps.Marker({
                    position: position,
                    map: this.map,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                });
                google.maps.event.addListener(this.marker, 'dragend', (event) => {
                    this.formData.latitude = event.latLng.lat();
                    this.formData.longitude = event.latLng.lng();
                    this.reverseGeocode();
                });
            }
            this.reverseGeocode();
        },

        reverseGeocode() {
            if (!this.formData.latitude || !this.formData.longitude) return;
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                location: {
                    lat: parseFloat(this.formData.latitude),
                    lng: parseFloat(this.formData.longitude)
                }
            }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    this.formData.address_text = results[0].formatted_address;
                }
            });
        },

        async startRecording() {
            // ✅ FIX 9: Kiểm tra HTTPS / secure context
            if (!window.isSecureContext) {
                Swal.fire('Lỗi', 'Ghi âm chỉ hoạt động trên HTTPS. Vui lòng dùng HTTPS hoặc localhost.', 'error');
                return;
            }
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                Swal.fire('Lỗi', 'Trình duyệt không hỗ trợ ghi âm', 'error');
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                // ✅ FIX 10: Chọn MIME type được hỗ trợ
                const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                    ? 'audio/webm;codecs=opus'
                    : MediaRecorder.isTypeSupported('audio/webm')
                        ? 'audio/webm'
                        : '';

                this.mediaRecorder = mimeType
                    ? new MediaRecorder(stream, { mimeType })
                    : new MediaRecorder(stream);

                let chunks = [];
                this.mediaRecorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };
                this.mediaRecorder.onstop = () => {
                    const type = this.mediaRecorder.mimeType || 'audio/webm';
                    this.audioBlob = new Blob(chunks, { type });
                    this.audioUrl = URL.createObjectURL(this.audioBlob);
                    this.isRecording = false;
                    clearInterval(this.timer);
                };

                this.mediaRecorder.start(100); // collect data every 100ms
                this.isRecording = true;
                this.recordingTime = 0;
                this.timer = setInterval(() => this.recordingTime++, 1000);

            } catch (err) {
                let msg = err.message;
                if (err.name === 'NotAllowedError') msg = 'Bạn đã từ chối quyền truy cập micro. Vui lòng cấp quyền trong cài đặt trình duyệt.';
                else if (err.name === 'NotFoundError') msg = 'Không tìm thấy micro. Vui lòng kiểm tra thiết bị.';
                Swal.fire('Lỗi', msg, 'error');
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        },

        resetRecording() {
            if (this.audioUrl) URL.revokeObjectURL(this.audioUrl);
            this.audioUrl = null;
            this.audioBlob = null;
            this.isRecording = false;
            clearInterval(this.timer);
        }
    }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places"></script>
@endpush
@endsection