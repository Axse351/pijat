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

                        <!-- Debug HUD -->
                        <div
                            class="absolute top-2 left-2 bg-black/80 text-white text-xs font-mono px-3 py-2 rounded-lg leading-6 min-w-[160px]">
                            EAR: <span id="earVal" class="text-yellow-300 font-bold">-</span><br>
                            Min EAR: <span id="minEarVal" class="text-orange-300">-</span><br>
                            Threshold: <span id="threshVal" class="text-cyan-300">0.25</span><br>
                            Mata: <span id="eyeStateVal">-</span><br>
                            Blink: <span id="debugBlink" class="text-green-300">0</span>
                        </div>

                        <!-- Blink flash feedback -->
                        <div id="blinkFlash"
                            class="hidden absolute inset-0 bg-white/30 pointer-events-none rounded-2xl"></div>

                        <div id="blinkIndicator"
                            class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/70 text-white text-sm font-semibold px-4 py-2 rounded-full whitespace-nowrap">
                            👁️ Kedipkan mata untuk konfirmasi...
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

                    <!-- Blink progress -->
                    <div id="blinkProgressBox" class="hidden w-full max-w-lg">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <span>Progress kedipan</span>
                            <span><span id="blinkCount">0</span>/2</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                            <div id="blinkBar" class="bg-orange-500 h-4 rounded-full transition-all duration-200"
                                style="width:0%"></div>
                        </div>
                    </div>

                    <!-- Threshold control -->
                    <div
                        class="w-full max-w-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">🔧 Kalibrasi
                                Sensitivitas Kedipan</p>
                            <button id="autoCalibBtn"
                                class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-full transition font-semibold">
                                ⚡ Auto-kalibrasi
                            </button>
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs text-amber-700 dark:text-amber-400 w-24 shrink-0">Threshold:</span>
                            <input type="range" id="threshSlider" min="0.10" max="0.40" step="0.005"
                                value="0.25" class="flex-1 accent-amber-500">
                            <span id="threshDisplay"
                                class="text-sm font-mono font-bold w-12 text-center text-amber-700 dark:text-amber-300">0.25</span>
                        </div>
                        <p class="text-xs text-amber-600 dark:text-amber-400">
                            💡 Klik <strong>Auto-kalibrasi</strong> lalu kedipkan mata 3x dalam 3 detik untuk set
                            threshold otomatis.
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
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        window.addEventListener('load', async () => {

            // ── DOM refs ──
            const video = document.getElementById('video');
            const overlay = document.getElementById('overlay');
            const statusText = document.getElementById('statusText');
            const statusSub = document.getElementById('statusSub');
            const blinkIndicator = document.getElementById('blinkIndicator');
            const blinkProgressBox = document.getElementById('blinkProgressBox');
            const blinkBar = document.getElementById('blinkBar');
            const blinkCountEl = document.getElementById('blinkCount');
            const successOverlay = document.getElementById('successOverlay');
            const errorOverlay = document.getElementById('errorOverlay');
            const earValEl = document.getElementById('earVal');
            const minEarValEl = document.getElementById('minEarVal');
            const eyeStateEl = document.getElementById('eyeStateVal');
            const debugBlinkEl = document.getElementById('debugBlink');
            const threshSlider = document.getElementById('threshSlider');
            const threshDisplay = document.getElementById('threshDisplay');
            const threshValEl = document.getElementById('threshVal');
            const blinkFlash = document.getElementById('blinkFlash');
            const autoCalibBtn = document.getElementById('autoCalibBtn');

            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';
            const BLINKS_NEEDED = 2;
            let EAR_THRESHOLD = 0.25;

            // Slider
            threshSlider.addEventListener('input', () => {
                EAR_THRESHOLD = parseFloat(threshSlider.value);
                threshDisplay.textContent = EAR_THRESHOLD.toFixed(3);
                threshValEl.textContent = EAR_THRESHOLD.toFixed(3);
            });

            // ── Load models — pakai faceLandmark68Net (bukan tiny) ──
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

            // ── EAR helpers ──
            function dist(a, b) {
                return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);
            }

            function calcEAR(pts, idx) {
                return (dist(pts[idx[1]], pts[idx[5]]) + dist(pts[idx[2]], pts[idx[4]])) /
                    (2.0 * dist(pts[idx[0]], pts[idx[3]]));
            }

            function getEAR(landmarks) {
                const p = landmarks.positions;
                return (calcEAR(p, [36, 37, 38, 39, 40, 41]) + calcEAR(p, [42, 43, 44, 45, 46, 47])) / 2;
            }

            // ── Auto-calibrate ──
            let isCalibrating = false;
            let calibSamples = [];

            autoCalibBtn.addEventListener('click', () => {
                if (isCalibrating) return;
                isCalibrating = true;
                calibSamples = [];
                autoCalibBtn.textContent = '⏳ Kedipkan mata 3x sekarang...';
                autoCalibBtn.disabled = true;

                setTimeout(() => {
                    isCalibrating = false;
                    autoCalibBtn.textContent = '⚡ Auto-kalibrasi';
                    autoCalibBtn.disabled = false;

                    if (calibSamples.length === 0) return;

                    const minEAR = Math.min(...calibSamples);
                    const maxEAR = Math.max(...calibSamples);
                    const newThresh = parseFloat(((minEAR + maxEAR) / 2).toFixed(3));

                    EAR_THRESHOLD = newThresh;
                    threshSlider.value = newThresh;
                    threshDisplay.textContent = newThresh.toFixed(3);
                    threshValEl.textContent = newThresh.toFixed(3);

                    setStatus('✅ Kalibrasi selesai! Threshold = ' + newThresh,
                        'Sekarang coba kedipkan mata', 'green');
                }, 3000);
            });

            // ── State ──
            const ctx = overlay.getContext('2d');
            let isProcessing = false;
            let blinkState = null;
            let earBuf = [];
            let minEarSession = 1.0;
            let blinkPhase = 'OPEN'; // 'OPEN' | 'CLOSING'
            let closeFrameCount = 0;
            const CLOSE_FRAMES_MIN = 1;

            // ── Loop utama via requestAnimationFrame ──
            let lastDetectTime = 0;
            const DETECT_INTERVAL_MS = 80;

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
                    .withFaceLandmarks() // 68-point penuh
                    .withFaceDescriptor();

                if (!det) {
                    earValEl.textContent = '-';
                    eyeStateEl.textContent = '-';
                    if (!blinkState) setStatus('👁️ Siap mendeteksi wajah', 'Posisikan wajah ke kamera',
                        'blue');
                    return;
                }

                // Box + label
                const box = det.detection.box;
                const match = faceMatcher.findBestMatch(det.descriptor);
                const isKnown = match.label !== 'unknown';

                ctx.strokeStyle = isKnown ? '#f97316' : '#ef4444';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
                ctx.fillStyle = isKnown ? '#f97316' : '#ef4444';
                ctx.font = 'bold 14px sans-serif';
                ctx.fillText(
                    isKnown ?
                    JSON.parse(match.label).name + ' (' + Math.round((1 - match.distance) * 100) +
                    '%)' :
                    'Tidak dikenal',
                    box.x, box.y > 20 ? box.y - 6 : box.y + box.height + 16
                );

                // Outline mata
                const pts = det.landmarks.positions;
                [
                    [36, 37, 38, 39, 40, 41],
                    [42, 43, 44, 45, 46, 47]
                ].forEach(eye => {
                    ctx.beginPath();
                    ctx.strokeStyle = '#fed7aa'; // orange untuk checkout
                    ctx.lineWidth = 1.5;
                    eye.forEach((i, k) => k === 0 ? ctx.moveTo(pts[i].x, pts[i].y) : ctx.lineTo(pts[
                        i].x, pts[i].y));
                    ctx.closePath();
                    ctx.stroke();
                });

                // EAR + smoothing
                const rawEAR = getEAR(det.landmarks);
                earBuf.push(rawEAR);
                if (earBuf.length > 3) earBuf.shift();
                const ear = earBuf.reduce((a, b) => a + b, 0) / earBuf.length;

                if (ear < minEarSession) minEarSession = ear;
                if (isCalibrating) calibSamples.push(rawEAR);

                earValEl.textContent = ear.toFixed(3);
                minEarValEl.textContent = minEarSession.toFixed(3);
                eyeStateEl.textContent = ear < EAR_THRESHOLD ? '😑 MENUTUP' : '👁️ TERBUKA';
                eyeStateEl.style.color = ear < EAR_THRESHOLD ? '#fbbf24' : '#86efac';

                if (!isKnown) {
                    blinkState = null;
                    blinkPhase = 'OPEN';
                    closeFrameCount = 0;
                    blinkProgressBox.classList.add('hidden');
                    blinkIndicator.classList.add('hidden');
                    setStatus('❓ Wajah tidak dikenal', 'Wajah tidak terdaftar di sistem', 'red');
                    return;
                }

                const info = JSON.parse(match.label);

                if (!blinkState || blinkState.id !== info.id) {
                    blinkState = {
                        id: info.id,
                        name: info.name,
                        blinkCount: 0
                    };
                    blinkPhase = 'OPEN';
                    closeFrameCount = 0;
                    earBuf = [];
                    minEarSession = 1.0;
                    blinkProgressBox.classList.remove('hidden');
                    blinkIndicator.classList.remove('hidden');
                    updateBlinkUI(0);
                    setStatus('✅ ' + info.name + ' terdeteksi!',
                        '👁️ Kedipkan mata ' + BLINKS_NEEDED + 'x untuk check-out', 'green');
                }

                debugBlinkEl.textContent = blinkState.blinkCount;

                // State machine kedipan
                if (blinkPhase === 'OPEN') {
                    if (ear < EAR_THRESHOLD) {
                        blinkPhase = 'CLOSING';
                        closeFrameCount = 1;
                    }
                } else if (blinkPhase === 'CLOSING') {
                    if (ear < EAR_THRESHOLD) {
                        closeFrameCount++;
                    } else {
                        // Mata terbuka lagi = 1 kedipan valid
                        if (closeFrameCount >= CLOSE_FRAMES_MIN) {
                            blinkState.blinkCount++;
                            updateBlinkUI(blinkState.blinkCount);
                            debugBlinkEl.textContent = blinkState.blinkCount;
                            console.log('✅ BLINK #' + blinkState.blinkCount,
                                '| frames:', closeFrameCount,
                                '| EAR:', ear.toFixed(3));

                            // Flash feedback
                            blinkFlash.classList.remove('hidden');
                            setTimeout(() => blinkFlash.classList.add('hidden'), 150);

                            if (blinkState.blinkCount >= BLINKS_NEEDED) {
                                isProcessing = true;
                                blinkIndicator.classList.add('hidden');
                                setStatus('⏳ Menyimpan check-out...', '', 'blue');
                                await doCheckOut(det, info);
                            }
                        }
                        blinkPhase = 'OPEN';
                        closeFrameCount = 0;
                    }
                }
            }

            requestAnimationFrame(detectLoop);

            // ── UI helpers ──
            function updateBlinkUI(count) {
                blinkCountEl.textContent = count;
                blinkBar.style.width = Math.min((count / BLINKS_NEEDED) * 100, 100) + '%';
            }

            async function doCheckOut(det, info) {
                try {
                    const snap = document.createElement('canvas');
                    snap.width = video.videoWidth;
                    snap.height = video.videoHeight;
                    snap.getContext('2d').drawImage(video, 0, 0);
                    const blob = await new Promise(r => snap.toBlob(r, 'image/jpeg', 0.9));

                    const fd = new FormData();
                    fd.append('_token', CSRF_TOKEN);
                    fd.append('therapist_id', info.id);
                    fd.append('confidence', (1 - det.detection.score).toFixed(4));
                    fd.append('image', blob, 'checkout.jpg');

                    const res = await fetch(CHECKOUT_URL, {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();

                    if (data.success) {
                        document.getElementById('successName').textContent = '✅ ' + info.name;
                        document.getElementById('successTime').textContent = 'Jam keluar: ' + data.time;
                        document.getElementById('workDuration').textContent = '⏱ Durasi: ' + data.duration;
                        successOverlay.classList.remove('hidden');
                        updateRowCheckout(info.id, data.time, data.duration);
                        setStatus('✅ Check-out berhasil!', info.name + ' — ' + data.time, 'green');
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
                    blinkState = null;
                    blinkPhase = 'OPEN';
                    closeFrameCount = 0;
                    earBuf = [];
                    minEarSession = 1.0;
                    isProcessing = false;
                    blinkProgressBox.classList.add('hidden');
                    blinkIndicator.classList.add('hidden');
                    updateBlinkUI(0);
                    setStatus('👁️ Siap mendeteksi wajah', 'Posisikan wajah ke kamera', 'blue');
                }, 4000);
            }

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
