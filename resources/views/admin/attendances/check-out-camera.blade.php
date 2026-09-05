<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📷 Absen Keluar (Check-out)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <div id="statusBar"
                class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg text-center">
                <p id="statusText" class="text-lg font-bold text-blue-800 dark:text-blue-200">⏳ Memuat sistem...</p>
                <p id="statusSub" class="text-sm text-blue-600 dark:text-blue-400 mt-1">Harap tunggu</p>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col items-center gap-4">

                    <!-- Video -->
                    <div class="relative w-full max-w-lg bg-black rounded-2xl overflow-hidden" style="aspect-ratio:4/3">
                        <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                        <!-- HUD ringkas -->
                        <div
                            class="absolute top-2 left-2 bg-black/80 text-white text-xs font-mono px-3 py-2 rounded-lg leading-6 min-w-[170px]">
                            Wajah: <span id="faceStateVal" class="text-yellow-300 font-bold">-</span><br>
                            Nama: <span id="nameVal" class="text-orange-300">-</span><br>
                            Lokasi: <span id="gpsStateVal" class="text-cyan-300">Mencari...</span><br>
                            Jarak ke Koichi: <span id="distanceVal" class="text-orange-300">-</span>
                        </div>

                        <div id="successOverlay"
                            class="hidden absolute inset-0 bg-orange-600/85 flex flex-col items-center justify-center text-white rounded-2xl">
                            <div class="text-6xl mb-3">✅</div>
                            <p id="successName" class="text-2xl font-bold"></p>
                            <p id="successTime" class="text-lg mt-1"></p>
                            <p id="workDuration" class="text-base mt-2 bg-white/20 px-4 py-1 rounded-full"></p>
                        </div>

                        <div id="errorOverlay"
                            class="hidden absolute inset-0 bg-red-600/85 flex flex-col items-center justify-center text-white rounded-2xl">
                            <div class="text-5xl mb-3">❌</div>
                            <p id="errorMsg" class="text-lg font-bold px-6 text-center"></p>
                        </div>

                        <div id="cameraError"
                            class="hidden absolute inset-0 flex items-center justify-center bg-gray-900 text-white text-sm p-4 text-center rounded-2xl">
                            ⚠️ Kamera tidak dapat diakses.
                        </div>
                    </div>

                    <!-- Tombol capture -->
                    <button type="button" id="captureBtn" disabled
                        class="w-full max-w-lg px-6 py-4 bg-orange-600 hover:bg-orange-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl transition">
                        📸 Ambil Foto & Absen Keluar
                    </button>
                    <p id="captureHint" class="text-xs text-gray-500 dark:text-gray-400 text-center -mt-2">
                        Posisikan wajah ke kamera dan pastikan lokasi sudah terdeteksi.
                    </p>

                    <!-- Status lokasi -->
                    <div id="locationBox"
                        class="w-full max-w-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">📍 Status Lokasi</p>
                            <button id="retryLocationBtn"
                                class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-full transition font-semibold">
                                🔄 Coba Lagi
                            </button>
                        </div>
                        <p id="locationText" class="text-xs text-amber-700 dark:text-amber-400">
                            Meminta izin akses lokasi dari browser...
                        </p>
                    </div>

                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('admin.attendances.index') }}"
                        class="px-6 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition inline-block">
                        ← Kembali
                    </a>
                </div>
            </div>

            <!-- Tabel status -->
            <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">📋 Status Hari Ini</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-center">Masuk</th>
                                <th class="px-3 py-2 text-center">Keluar</th>
                                <th class="px-3 py-2 text-center">Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($therapists as $t)
                                @php $att = $t->attendances->first(); @endphp
                                <tr id="row-{{ $t->id }}">
                                    <td class="px-3 py-2 font-medium">{{ $t->name }}</td>
                                    <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">
                                        {{ $att && $att->check_in_at ? $att->check_in_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-center" id="checkout-{{ $t->id }}">
                                        @if ($att && $att->check_out_at)
                                            <span
                                                class="text-orange-600 font-semibold">{{ $att->check_out_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center text-gray-500" id="duration-{{ $t->id }}">
                                        @if ($att && $att->check_in_at && $att->check_out_at)
                                            {{ $att->check_in_at->diffInHours($att->check_out_at) }}j
                                            {{ $att->check_in_at->diff($att->check_out_at)->i }}m
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const THERAPIST_DESCRIPTORS = @json($faceDescriptors);
        const CHECKOUT_URL = "{{ route('admin.attendance.check-out-ajax') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const OFFICE_LAT = {{ $officeLatitude }};
        const OFFICE_LNG = {{ $officeLongitude }};
        const MAX_DISTANCE_METERS = {{ $maxDistanceMeters }};
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        window.addEventListener('load', async () => {

            // ── DOM refs ──
            const video = document.getElementById('video');
            const overlay = document.getElementById('overlay');
            const statusText = document.getElementById('statusText');
            const statusSub = document.getElementById('statusSub');
            const successOverlay = document.getElementById('successOverlay');
            const errorOverlay = document.getElementById('errorOverlay');
            const faceStateVal = document.getElementById('faceStateVal');
            const nameVal = document.getElementById('nameVal');
            const gpsStateVal = document.getElementById('gpsStateVal');
            const distanceVal = document.getElementById('distanceVal');
            const captureBtn = document.getElementById('captureBtn');
            const captureHint = document.getElementById('captureHint');
            const locationText = document.getElementById('locationText');
            const retryLocationBtn = document.getElementById('retryLocationBtn');

            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';

            // ── Load models ──
            setStatus('⏳ Memuat model...', 'Harap tunggu', 'blue');
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
            } catch (e) {
                setStatus('❌ Gagal memuat model', e.message, 'red');
                return;
            }

            // ── Build face matcher ──
            const labeled = [];
            for (const t of THERAPIST_DESCRIPTORS) {
                if (!t.embeddings || t.embeddings.length === 0) continue;
                try {
                    labeled.push(new faceapi.LabeledFaceDescriptors(
                        JSON.stringify({
                            id: t.id,
                            name: t.name
                        }),
                        [new Float32Array(t.embeddings)]
                    ));
                } catch (e) {
                    console.warn('Skip', t.name, e);
                }
            }

            if (labeled.length === 0) {
                setStatus('⚠️ Tidak ada wajah terdaftar', 'Daftarkan wajah terapis terlebih dahulu', 'yellow');
                return;
            }
            const faceMatcher = new faceapi.FaceMatcher(labeled, 0.5);

            // ── Kamera ──
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
                return;
            }
            await new Promise(r => video.addEventListener('playing', r, {
                once: true
            }));
            setStatus('👁️ Siap mendeteksi wajah', 'Posisikan wajah ke kamera', 'blue');

            // ════════════════════════════════════════════════
            // GEOLOCATION — pantau posisi terus-menerus
            // ════════════════════════════════════════════════
            let lastPosition = null;
            let geoWatchId = null;

            function haversineMeters(lat1, lng1, lat2, lng2) {
                const R = 6371000;
                const toRad = d => d * Math.PI / 180;
                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2 +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function updateDistanceDisplay() {
                if (!lastPosition) {
                    distanceVal.textContent = '-';
                    return;
                }
                const dist = haversineMeters(OFFICE_LAT, OFFICE_LNG, lastPosition.lat, lastPosition.lng);
                distanceVal.textContent = Math.round(dist) + ' m';
                distanceVal.style.color = dist <= MAX_DISTANCE_METERS ? '#86efac' : '#fca5a5';
            }

            function startLocationWatch() {
                if (!('geolocation' in navigator)) {
                    locationText.textContent = '❌ Browser ini tidak mendukung geolocation.';
                    gpsStateVal.textContent = 'Tidak didukung';
                    captureHint.textContent = 'Perangkat tidak mendukung deteksi lokasi.';
                    return;
                }

                gpsStateVal.textContent = 'Mencari...';
                locationText.textContent = 'Meminta izin akses lokasi dari browser...';

                if (geoWatchId !== null) navigator.geolocation.clearWatch(geoWatchId);

                geoWatchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        lastPosition = {
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                            accuracy: pos.coords.accuracy
                        };
                        gpsStateVal.textContent = 'Terdeteksi ✓';
                        locationText.textContent =
                            `📍 Lokasi ditemukan (akurasi ±${Math.round(pos.coords.accuracy)} m).`;
                        updateDistanceDisplay();
                        refreshCaptureButton();
                    },
                    (err) => {
                        lastPosition = null;
                        gpsStateVal.textContent = 'Gagal';
                        let msg = 'Gagal mendapatkan lokasi.';
                        if (err.code === err.PERMISSION_DENIED) {
                            msg =
                            '❌ Izin lokasi ditolak. Aktifkan izin lokasi di browser untuk bisa absen.';
                        } else if (err.code === err.TIMEOUT) {
                            msg = '⏳ Waktu pencarian lokasi habis. Klik "Coba Lagi".';
                        }
                        locationText.textContent = msg;
                        refreshCaptureButton();
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 5000
                    }
                );
            }

            retryLocationBtn.addEventListener('click', startLocationWatch);
            startLocationWatch();

            // ════════════════════════════════════════════════
            // DETEKSI WAJAH (tanpa liveness kedip — tinggal foto)
            // ════════════════════════════════════════════════
            const ctx = overlay.getContext('2d');
            let isProcessing = false;
            let currentMatch = null;

            function refreshCaptureButton() {
                const hasFace = !!currentMatch;
                const hasLocation = !!lastPosition;
                captureBtn.disabled = isProcessing || !hasFace || !hasLocation;

                if (isProcessing) {
                    captureHint.textContent = 'Sedang memproses absen...';
                } else if (!hasFace && !hasLocation) {
                    captureHint.textContent = 'Posisikan wajah ke kamera dan aktifkan izin lokasi.';
                } else if (!hasFace) {
                    captureHint.textContent = 'Posisikan wajah ke kamera untuk dikenali sistem.';
                } else if (!hasLocation) {
                    captureHint.textContent = 'Menunggu lokasi terdeteksi...';
                } else {
                    captureHint.textContent =
                        `Siap! Klik tombol untuk absen keluar sebagai ${currentMatch.name}.`;
                }
            }

            let lastDetectTime = 0;
            const DETECT_INTERVAL_MS = 150;

            async function detectLoop(timestamp) {
                requestAnimationFrame(detectLoop);

                if (isProcessing) return;
                if (timestamp - lastDetectTime < DETECT_INTERVAL_MS) return;
                lastDetectTime = timestamp;

                overlay.width = video.videoWidth;
                overlay.height = video.videoHeight;
                ctx.clearRect(0, 0, overlay.width, overlay.height);

                const det = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.4
                    }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!det) {
                    currentMatch = null;
                    faceStateVal.textContent = 'Tidak ada wajah';
                    nameVal.textContent = '-';
                    refreshCaptureButton();
                    return;
                }

                const box = det.detection.box;
                const match = faceMatcher.findBestMatch(det.descriptor);
                const isKnown = match.label !== 'unknown';

                ctx.strokeStyle = isKnown ? '#f97316' : '#ef4444';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);

                if (isKnown) {
                    const info = JSON.parse(match.label);
                    currentMatch = info;
                    faceStateVal.textContent = 'Dikenali ✓';
                    nameVal.textContent = info.name + ' (' + Math.round((1 - match.distance) * 100) + '%)';
                    ctx.fillStyle = '#f97316';
                    ctx.font = 'bold 14px sans-serif';
                    ctx.fillText(info.name, box.x, box.y > 20 ? box.y - 6 : box.y + box.height + 16);
                    setStatus('✅ ' + info.name + ' terdeteksi', 'Klik tombol untuk absen keluar', 'green');
                } else {
                    currentMatch = null;
                    faceStateVal.textContent = 'Tidak dikenal';
                    nameVal.textContent = '-';
                    ctx.fillStyle = '#ef4444';
                    ctx.font = 'bold 14px sans-serif';
                    ctx.fillText('Tidak dikenal', box.x, box.y > 20 ? box.y - 6 : box.y + box.height + 16);
                    setStatus('❓ Wajah tidak dikenal', 'Wajah tidak terdaftar di sistem', 'red');
                }

                refreshCaptureButton();
            }

            requestAnimationFrame(detectLoop);

            // ════════════════════════════════════════════════
            // CAPTURE & SUBMIT
            // ════════════════════════════════════════════════
            captureBtn.addEventListener('click', async () => {
                if (!currentMatch || !lastPosition || isProcessing) return;
                isProcessing = true;
                refreshCaptureButton();
                setStatus('⏳ Menyimpan check-out...', '', 'blue');

                try {
                    const snap = document.createElement('canvas');
                    snap.width = video.videoWidth;
                    snap.height = video.videoHeight;
                    snap.getContext('2d').drawImage(video, 0, 0);
                    const blob = await new Promise(r => snap.toBlob(r, 'image/jpeg', 0.9));

                    const fd = new FormData();
                    fd.append('_token', CSRF_TOKEN);
                    fd.append('therapist_id', currentMatch.id);
                    fd.append('confidence', '1.0');
                    fd.append('image', blob, 'checkout.jpg');
                    fd.append('latitude', lastPosition.lat);
                    fd.append('longitude', lastPosition.lng);

                    const res = await fetch(CHECKOUT_URL, {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();

                    if (data.success) {
                        document.getElementById('successName').textContent = '✅ ' + currentMatch
                            .name;
                        document.getElementById('successTime').textContent = 'Jam keluar: ' + data
                            .time;
                        document.getElementById('workDuration').textContent = '⏱ Durasi: ' + data
                            .duration;
                        successOverlay.classList.remove('hidden');
                        updateRowCheckout(currentMatch.id, data.time, data.duration);
                        setStatus('✅ Check-out berhasil!', currentMatch.name + ' — ' + data.time,
                            'green');
                    } else {
                        document.getElementById('errorMsg').textContent = data.message;
                        errorOverlay.classList.remove('hidden');
                        setStatus('❌ ' + data.message, '', 'red');
                    }
                } catch (e) {
                    document.getElementById('errorMsg').textContent = 'Error: ' + e.message;
                    errorOverlay.classList.remove('hidden');
                }

                setTimeout(() => {
                    successOverlay.classList.add('hidden');
                    errorOverlay.classList.add('hidden');
                    isProcessing = false;
                    refreshCaptureButton();
                    setStatus('👁️ Siap mendeteksi wajah', 'Posisikan wajah ke kamera',
                        'blue');
                }, 4000);
            });

            // ── UI helpers ──
            function updateRowCheckout(id, time, duration) {
                const el = document.getElementById('checkout-' + id);
                if (el) el.innerHTML = '<span class="text-orange-600 font-semibold">' + time + '</span>';
                const dur = document.getElementById('duration-' + id);
                if (dur) dur.textContent = duration;
            }

            function setStatus(text, sub, color) {
                const p = {
                    blue: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200',
                    green: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200',
                    yellow: 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200',
                    red: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200',
                };
                document.getElementById('statusBar').className = 'mb-4 p-4 border rounded-lg text-center ' + (p[
                    color] || p.blue);
                statusText.textContent = text;
                statusSub.textContent = sub;
            }
        });
    </script>
</x-app-layout>
