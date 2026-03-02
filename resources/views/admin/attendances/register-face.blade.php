<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrasi Wajah') }} — {{ $therapist->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div
                    class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($faceData)
                <div
                    class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">Data Wajah Sudah Ada</p>
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        Status: <strong>{{ $faceData->getStatusLabel() }}</strong>
                        — Terdaftar: {{ $faceData->getRegisteredAtFormatted() }}
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <!-- Model loading status -->
                <div id="modelStatus"
                    class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg text-sm text-yellow-800 dark:text-yellow-300">
                    ⏳ Memuat model face recognition... Harap tunggu.
                </div>

                <!-- Kamera -->
                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-full max-w-md bg-black rounded-xl overflow-hidden" style="aspect-ratio:4/3">
                        <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                        <div id="cameraError"
                            class="hidden absolute inset-0 flex items-center justify-center bg-gray-900 text-white text-sm p-4 text-center">
                            ⚠️ Kamera tidak dapat diakses. Pastikan izin kamera diberikan di browser.
                        </div>
                    </div>

                    <p id="faceStatus" class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                        Menunggu model dimuat...
                    </p>

                    <!-- Preview foto -->
                    <div id="previewBox" class="hidden w-full max-w-md">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Foto yang akan disimpan:
                        </p>
                        <img id="previewImg" src="" alt="Preview"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600">
                    </div>

                    <div class="flex gap-3 flex-wrap justify-center">
                        <button type="button" id="captureBtn" disabled
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition">
                            📸 Ambil Foto & Generate Embeddings
                        </button>
                        <button type="button" id="retakeBtn"
                            class="hidden px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                            🔄 Ulangi
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <form id="registerForm" action="{{ route('admin.therapist-face.store', $therapist->id) }}"
                    method="POST" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    <input type="file" id="imageFile" name="image" class="hidden" accept="image/*">
                    <input type="hidden" id="embeddings" name="embeddings" value="">

                    <div id="embeddingsInfo"
                        class="hidden mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-300">
                        ✅ Embeddings (128 dimensi) berhasil digenerate! Siap disimpan.
                    </div>

                    <div class="flex gap-3 flex-wrap">
                        <button type="submit" id="submitBtn" disabled
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition">
                            💾 Simpan Data Wajah
                        </button>
                        <a href="{{ route('admin.attendances.index') }}"
                            class="px-6 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <div
                class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-sm text-yellow-800 dark:text-yellow-300">
                <p class="font-semibold mb-1">📋 Petunjuk:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Tunggu kotak hijau muncul di wajah sebelum ambil foto</li>
                    <li>Pastikan pencahayaan cukup dan wajah menghadap lurus ke kamera</li>
                    <li>Embeddings 128 dimensi akan digenerate otomatis dari foto</li>
                    <li>Setelah disimpan, admin perlu memverifikasi sebelum bisa absen</li>
                </ul>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        window.addEventListener('load', async () => {
            const video = document.getElementById('video');
            const overlay = document.getElementById('overlay');
            const captureBtn = document.getElementById('captureBtn');
            const retakeBtn = document.getElementById('retakeBtn');
            const submitBtn = document.getElementById('submitBtn');
            const previewBox = document.getElementById('previewBox');
            const previewImg = document.getElementById('previewImg');
            const imageFile = document.getElementById('imageFile');
            const embInput = document.getElementById('embeddings');
            const faceStatus = document.getElementById('faceStatus');
            const modelStatus = document.getElementById('modelStatus');
            const embeddingsInfo = document.getElementById('embeddingsInfo');

            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';

            // ── Load models ──
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
                modelStatus.innerHTML = '✅ Model siap digunakan!';
                modelStatus.className =
                    'mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-300';
            } catch (e) {
                modelStatus.innerHTML = '❌ Gagal memuat model: ' + e.message;
                modelStatus.className =
                    'mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700';
                return;
            }

            // ── Start kamera ──
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: 640,
                        height: 480
                    },
                    audio: false
                });
                video.srcObject = stream;
            } catch (err) {
                document.getElementById('cameraError').classList.remove('hidden');
                video.classList.add('hidden');
                return;
            }
            await new Promise(r => video.addEventListener('playing', r, {
                once: true
            }));

            // ── Live detection loop ──
            const ctx = overlay.getContext('2d');
            let detectionInterval = null;
            let faceDetected = false;

            function startDetection() {
                detectionInterval = setInterval(async () => {
                    overlay.width = video.videoWidth;
                    overlay.height = video.videoHeight;
                    ctx.clearRect(0, 0, overlay.width, overlay.height);

                    const det = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                            inputSize: 320,
                            scoreThreshold: 0.5
                        }))
                        .withFaceLandmarks(true);

                    if (det) {
                        faceDetected = true;
                        captureBtn.disabled = false;
                        faceStatus.textContent = '✅ Wajah terdeteksi! Siap ambil foto.';
                        faceStatus.className =
                            'text-sm font-semibold text-green-600 dark:text-green-400';
                        const b = det.detection.box;
                        ctx.strokeStyle = '#22c55e';
                        ctx.lineWidth = 3;
                        ctx.strokeRect(b.x, b.y, b.width, b.height);
                    } else {
                        faceDetected = false;
                        captureBtn.disabled = true;
                        faceStatus.textContent = '⏳ Posisikan wajah ke kamera...';
                        faceStatus.className =
                            'text-sm font-semibold text-yellow-600 dark:text-yellow-400';
                    }
                }, 300);
            }
            startDetection();

            // ── Capture & generate embeddings ──
            captureBtn.addEventListener('click', async () => {
                if (!faceDetected) return;
                clearInterval(detectionInterval);
                ctx.clearRect(0, 0, overlay.width, overlay.height);

                const snap = document.createElement('canvas');
                snap.width = video.videoWidth;
                snap.height = video.videoHeight;
                snap.getContext('2d').drawImage(video, 0, 0);

                faceStatus.textContent = '⏳ Generating embeddings 128 dimensi...';
                faceStatus.className = 'text-sm font-semibold text-blue-600 dark:text-blue-400';

                const det = await faceapi
                    .detectSingleFace(snap, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 320,
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();

                if (!det) {
                    faceStatus.textContent = '❌ Wajah tidak terdeteksi di foto. Coba lagi.';
                    faceStatus.className = 'text-sm font-semibold text-red-600 dark:text-red-400';
                    startDetection();
                    return;
                }

                // Simpan embeddings sebagai JSON array
                embInput.value = JSON.stringify(Array.from(det.descriptor));

                previewImg.src = snap.toDataURL('image/jpeg', 0.9);
                previewBox.classList.remove('hidden');
                embeddingsInfo.classList.remove('hidden');

                snap.toBlob(blob => {
                    const file = new File([blob], 'face_register.jpg', {
                        type: 'image/jpeg'
                    });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    imageFile.files = dt.files;
                    submitBtn.disabled = false;
                }, 'image/jpeg', 0.9);

                faceStatus.textContent = '✅ Embeddings siap! Klik "Simpan Data Wajah".';
                faceStatus.className = 'text-sm font-semibold text-green-600 dark:text-green-400';
                captureBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
            });

            // ── Retake ──
            retakeBtn.addEventListener('click', () => {
                previewBox.classList.add('hidden');
                embeddingsInfo.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                retakeBtn.classList.add('hidden');
                submitBtn.disabled = true;
                embInput.value = '';
                imageFile.value = '';
                faceStatus.textContent = '⏳ Mendeteksi wajah...';
                startDetection();
            });
        });
    </script>
</x-app-layout>
