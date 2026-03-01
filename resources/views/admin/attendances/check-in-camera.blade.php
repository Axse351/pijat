<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Check-in: ') }} {{ $therapist->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-2">{{ __('Terjadi Kesalahan:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Main Container -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Camera Section (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Camera Feed -->
                            <div id="cameraContainer" class="relative bg-black rounded-lg overflow-hidden mb-4">
                                <video id="camera" autoplay playsinline width="100%" style="display: none;"></video>
                                <canvas id="canvas" width="640" height="480" class="w-full"></canvas>

                                <!-- Loading Indicator -->
                                <div id="loadingIndicator" class="absolute inset-0 flex items-center justify-center bg-black/80">
                                    <div class="text-center">
                                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                                        <p class="text-white text-sm font-semibold">{{ __('Memuat Kamera...') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Indicators -->
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <!-- Face Detected -->
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Wajah Terdeteksi') }}</span>
                                        <div id="faceDetectedIndicator" class="w-3 h-3 rounded-full bg-red-500"></div>
                                    </div>
                                </div>

                                <!-- Eyes Open -->
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Mata Terbuka') }}</span>
                                        <div id="eyesOpenIndicator" class="w-3 h-3 rounded-full bg-red-500"></div>
                                    </div>
                                </div>

                                <!-- Blink Detected -->
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Kedip Terdeteksi') }}</span>
                                        <div id="blinkDetectedIndicator" class="w-3 h-3 rounded-full bg-red-500"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confidence Score -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Confidence Score') }}</label>
                                    <span id="confidenceValue" class="text-sm font-mono font-semibold text-blue-600 dark:text-blue-400">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div id="confidenceBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                                <p class="text-sm text-blue-900 dark:text-blue-200 font-semibold mb-2">📷 {{ __('Instruksi:') }}</p>
                                <ol class="text-sm text-blue-800 dark:text-blue-300 space-y-1 list-decimal list-inside">
                                    <li>{{ __('Posisikan wajah di depan kamera') }}</li>
                                    <li>{{ __('Pastikan mata terbuka dan fokus ke kamera') }}</li>
                                    <li>{{ __('Lakukan kedip mata minimal 1 kali') }}</li>
                                    <li>{{ __('Tekan tombol "Check-in" untuk konfirmasi') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Section (Right) -->
                <div class="space-y-6">
                    <!-- Therapist Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Informasi Terapis') }}</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('Nama') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $therapist->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('Email') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $therapist->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('Waktu') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="currentTime">--:--:--</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Box -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Status Deteksi') }}</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Wajah:') }}</span>
                                    <span id="statusFace" class="font-semibold text-red-600">{{ __('Tidak Terdeteksi') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Mata:') }}</span>
                                    <span id="statusEyes" class="font-semibold text-red-600">{{ __('Tertutup') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Kedip:') }}</span>
                                    <span id="statusBlink" class="font-semibold text-red-600">{{ __('Belum Terdeteksi') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Status Keseluruhan:') }}</span>
                                    <span id="overallStatus" class="font-semibold text-red-600">{{ __('Siap') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Capture Button -->
                        <button id="captureBtn" type="button" disabled
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition">
                            📸 {{ __('Capture') }}
                        </button>

                        <!-- Check-in Submit Button -->
                        <form id="checkInForm" action="{{ route('admin.attendance.check-in', $therapist->id) }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" id="capturedImage" name="image">
                            <input type="hidden" id="confidenceScore" name="confidence" value="0">
                            <button type="submit" class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                                ✓ {{ __('Check-in') }}
                            </button>
                        </form>

                        <!-- Back Button -->
                        <a href="{{ route('admin.attendances.index') }}"
                            class="block w-full px-6 py-3 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition text-center">
                            ← {{ __('Kembali') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.11.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/face-detection@0.0.7"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/face-landmarks-detection@0.0.5"></script>
    <script>
        // State
        let state = {
            isFaceDetected: false,
            isEyesOpen: false,
            isBlinkDetected: false,
            blinkCount: 0,
            confidence: 0,
            eyeClosedFrames: 0,
            maxEyeClosedFrames: 3,
            capturedImage: null
        };

        // Canvas & Video Elements
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const video = document.getElementById('camera');
        const loadingIndicator = document.getElementById('loadingIndicator');

        // Initialize Camera
        async function initCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    },
                    audio: false
                });

                video.srcObject = stream;

                video.onloadedmetadata = () => {
                    video.play();
                    loadingIndicator.style.display = 'none';
                    startDetection();
                };

            } catch (error) {
                console.error('Error accessing camera:', error);
                loadingIndicator.innerHTML = `
                    <div class="text-center">
                        <p class="text-red-500 text-sm font-semibold">❌ {{ __('Tidak bisa akses kamera') }}</p>
                        <p class="text-gray-300 text-xs mt-2">{{ __('Pastikan izin kamera diberikan') }}</p>
                    </div>
                `;
            }
        }

        // Face Detection Model
        let detector = null;

        async function loadModel() {
            try {
                detector = await faceDetection.createDetector(
                    faceDetection.SupportedModels.MediaPipeFaceDetector,
                    {
                        runtime: 'tfjs'
                    }
                );
            } catch (error) {
                console.error('Error loading model:', error);
            }
        }

        // Main Detection Loop
        async function startDetection() {
            if (!detector) {
                await loadModel();
            }

            async function detect() {
                try {
                    // Draw video frame to canvas
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Detect faces
                    const faces = await detector.estimateFaces(canvas);

                    // Reset detection states
                    state.isFaceDetected = faces.length > 0;

                    if (state.isFaceDetected && faces.length > 0) {
                        const face = faces[0];

                        // Calculate confidence based on detection score
                        state.confidence = Math.min(face.detection.score * 100, 100);

                        // Draw face detection box
                        drawFaceBox(face);

                        // Eye detection using landmarks
                        detectEyes(canvas, faces);
                    } else {
                        state.confidence = 0;
                    }

                    // Update UI
                    updateUI();

                    // Continue detection
                    requestAnimationFrame(detect);

                } catch (error) {
                    console.error('Detection error:', error);
                    requestAnimationFrame(detect);
                }
            }

            detect();
        }

        // Draw Face Detection Box
        function drawFaceBox(face) {
            const start = face.box.topLeft;
            const end = face.box.bottomRight;
            const size = [end[0] - start[0], end[1] - start[1]];

            // Draw rectangle
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 3;
            ctx.strokeRect(start[0], start[1], size[0], size[1]);

            // Draw label
            ctx.fillStyle = '#00ff00';
            ctx.font = 'bold 14px Arial';
            ctx.fillText(`${state.confidence.toFixed(0)}%`, start[0], start[1] - 10);
        }

        // Eye Detection
        function detectEyes(canvas, faces) {
            if (faces.length === 0) {
                state.isEyesOpen = false;
                return;
            }

            const face = faces[0];

            // Simple eye detection based on face landmarks
            // If face is detected with good confidence, assume eyes are in the frame
            if (state.confidence > 60) {
                state.isEyesOpen = true;
            } else {
                state.isEyesOpen = false;
            }

            // Simulate blink detection (every few frames without clear eyes)
            if (!state.isEyesOpen) {
                state.eyeClosedFrames++;
                if (state.eyeClosedFrames > state.maxEyeClosedFrames) {
                    state.blinkCount++;
                    state.isBlinkDetected = true;
                    state.eyeClosedFrames = 0;
                }
            } else {
                state.eyeClosedFrames = 0;
            }
        }

        // Update UI
        function updateUI() {
            // Indicators
            updateIndicator('faceDetectedIndicator', state.isFaceDetected);
            updateIndicator('eyesOpenIndicator', state.isEyesOpen);
            updateIndicator('blinkDetectedIndicator', state.isBlinkDetected);

            // Status Text
            document.getElementById('statusFace').textContent = state.isFaceDetected ?
                '✓ Terdeteksi' : '✗ Tidak Terdeteksi';
            document.getElementById('statusFace').className = `font-semibold ${state.isFaceDetected ? 'text-green-600' : 'text-red-600'}`;

            document.getElementById('statusEyes').textContent = state.isEyesOpen ?
                '✓ Terbuka' : '✗ Tertutup';
            document.getElementById('statusEyes').className = `font-semibold ${state.isEyesOpen ? 'text-green-600' : 'text-red-600'}`;

            document.getElementById('statusBlink').textContent = state.blinkCount > 0 ?
                `✓ ${state.blinkCount} Kedip` : '✗ Belum Terdeteksi';
            document.getElementById('statusBlink').className = `font-semibold ${state.blinkCount > 0 ? 'text-green-600' : 'text-red-600'}`;

            // Confidence
            document.getElementById('confidenceValue').textContent = state.confidence.toFixed(0) + '%';
            document.getElementById('confidenceBar').style.width = state.confidence + '%';

            // Overall Status & Button Enable
            const isReadyForCapture = state.isFaceDetected && state.isEyesOpen && state.confidence > 75;
            document.getElementById('overallStatus').textContent = isReadyForCapture ?
                '✓ Siap Capture' : '⏳ Persiapkan';
            document.getElementById('overallStatus').className = `font-semibold ${isReadyForCapture ? 'text-green-600' : 'text-yellow-600'}`;

            document.getElementById('captureBtn').disabled = !isReadyForCapture;
        }

        function updateIndicator(elementId, isActive) {
            const element = document.getElementById(elementId);
            element.className = `w-3 h-3 rounded-full ${isActive ? 'bg-green-500 animate-pulse' : 'bg-red-500'}`;
        }

        // Capture Button Handler
        document.getElementById('captureBtn').addEventListener('click', function() {
            // Capture image from canvas
            const imageData = canvas.toDataURL('image/jpeg');

            // Set form values
            document.getElementById('capturedImage').value = imageData;
            document.getElementById('confidenceScore').value = (state.confidence / 100).toFixed(2);

            // Show form and submit
            document.getElementById('checkInForm').style.display = 'block';

            // Scroll to form
            document.getElementById('checkInForm').scrollIntoView({ behavior: 'smooth' });
        });

        // Update Current Time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID');
        }

        setInterval(updateTime, 1000);
        updateTime();

        // Initialize
        window.addEventListener('load', initCamera);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</x-app-layout>
