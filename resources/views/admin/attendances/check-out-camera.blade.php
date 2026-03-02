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

            <!-- Status bar -->
            <div id="statusBar"
                class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg text-center">
                <p id="statusText" class="text-lg font-bold text-blue-800 dark:text-blue-200">⏳ Memuat sistem...</p>
                <p id="statusSub" class="text-sm text-blue-600 dark:text-blue-400 mt-1">Harap tunggu</p>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <div class="flex flex-col items-center gap-4">
                    <div class="relative w-full max-w-lg bg-black rounded-2xl overflow-hidden" style="aspect-ratio:4/3">
                        <video id="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                        <div id="blinkIndicator"
                            class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/70 text-white text-sm font-semibold px-4 py-2 rounded-full">
                            👁️ Kedipkan mata untuk konfirmasi...
                        </div>

                        <div id="successOverlay"
                            class="hidden absolute inset-0 bg-orange-500/80 flex flex-col items-center justify-center text-white">
                            <div class="text-6xl mb-3">✅</div>
                            <p id="successName" class="text-2xl font-bold"></p>
                            <p id="successTime" class="text-lg mt-1"></p>
                            <p id="workDuration" class="text-base mt-1"></p>
                        </div>

                        <div id="errorOverlay"
                            class="hidden absolute inset-0 bg-red-500/80 flex flex-col items-center justify-center text-white">
                            <div class="text-6xl mb-3">❌</div>
                            <p id="errorMsg" class="text-xl font-bold px-4 text-center"></p>
                        </div>

                        <div id="cameraError"
                            class="hidden absolute inset-0 flex items-center justify-center bg-gray-900 text-white text-sm p-4 text-center">
                            ⚠️ Kamera tidak dapat diakses.
                        </div>
                    </div>

                    <div id="blinkProgressBox" class="hidden w-full max-w-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Progress kedipan: <span
                                id="blinkCount">0</span>/2</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div id="blinkBar" class="bg-orange-500 h-3 rounded-full transition-all duration-300"
                                style="width:0%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('admin.attendances.index') }}"
                        class="px-6 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition inline-block">
                        ← Kembali
                    </a>
                </div>
            </div>

            <!-- Tabel status hari ini -->
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
                                                class="text-green-600 font-semibold">{{ $att->check_out_at->format('H:i') }}</span>
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

            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';

            setStatus('⏳ Memuat model...', 'Harap tunggu', 'blue');
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                ]);
            } catch (e) {
                setStatus('❌ Gagal memuat model', e.message, 'red');
                return;
            }

            const labeledDescriptors = [];
            for (const t of THERAPIST_DESCRIPTORS) {
                if (!t.embeddings || t.embeddings.length === 0) continue;
                try {
                    labeledDescriptors.push(
                        new faceapi.LabeledFaceDescriptors(
                            JSON.stringify({
                                id: t.id,
                                name: t.name
                            }),
                            [new Float32Array(t.embeddings)]
                        )
                    );
                } catch (e) {
                    console.warn('Skip', t.name, e);
                }
            }

            if (labeledDescriptors.length === 0) {
                setStatus('⚠️ Tidak ada wajah terdaftar', 'Daftarkan wajah terapis terlebih dahulu', 'yellow');
                return;
            }

            const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);

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
            setStatus('👁️ Siap mendeteksi wajah', 'Silakan berdiri di depan kamera', 'blue');

            const ctx = overlay.getContext('2d');
            let isProcessing = false;
            let blinkState = null;
            const EAR_THRESHOLD = 0.22;
            const BLINKS_NEEDED = 2;
            let eyeOpen = true;

            function eyeAspectRatio(landmarks) {
                const pts = landmarks.positions;

                function ear(indices) {
                    const [p1, p2, p3, p4, p5, p6] = indices.map(i => pts[i]);
                    const A = Math.hypot(p2.x - p6.x, p2.y - p6.y);
                    const B = Math.hypot(p3.x - p5.x, p3.y - p5.y);
                    const C = Math.hypot(p1.x - p4.x, p1.y - p4.y);
                    return (A + B) / (2.0 * C);
                }
                return (ear([36, 37, 38, 41, 40, 39]) + ear([42, 43, 44, 47, 46, 45])) / 2.0;
            }

            setInterval(async () => {
                if (isProcessing) return;

                overlay.width = video.videoWidth;
                overlay.height = video.videoHeight;
                ctx.clearRect(0, 0, overlay.width, overlay.height);

                const det = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 320,
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();

                if (!det) {
                    if (!blinkState) setStatus('👁️ Siap mendeteksi wajah',
                        'Posisikan wajah ke kamera', 'blue');
                    return;
                }

                const box = det.detection.box;
                const match = faceMatcher.findBestMatch(det.descriptor);
                const isKnown = match.label !== 'unknown';

                ctx.strokeStyle = isKnown ? '#f97316' : '#ef4444';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
                ctx.fillStyle = isKnown ? '#f97316' : '#ef4444';
                ctx.font = 'bold 14px sans-serif';
                const info = isKnown ?
                    JSON.parse(match.label).name + ' (' + Math.round((1 - match.distance) * 100) +
                    '%)' :
                    'Tidak dikenal';
                ctx.fillText(info, box.x, box.y > 20 ? box.y - 6 : box.y + box.height + 16);

                if (!isKnown) {
                    blinkState = null;
                    blinkProgressBox.classList.add('hidden');
                    blinkIndicator.classList.add('hidden');
                    setStatus('❓ Wajah tidak dikenal', 'Wajah tidak terdaftar di sistem', 'red');
                    return;
                }

                const therapistInfo = JSON.parse(match.label);
                const ear = eyeAspectRatio(det.landmarks);

                if (!blinkState || blinkState.id !== therapistInfo.id) {
                    blinkState = {
                        id: therapistInfo.id,
                        name: therapistInfo.name,
                        blinkCount: 0
                    };
                    blinkProgressBox.classList.remove('hidden');
                    blinkIndicator.classList.remove('hidden');
                    eyeOpen = true;
                    updateBlinkUI(0);
                    setStatus('✅ ' + therapistInfo.name + ' terdeteksi!', '👁️ Kedipkan mata ' +
                        BLINKS_NEEDED + 'x untuk check-out', 'green');
                }

                if (ear < EAR_THRESHOLD && eyeOpen) {
                    eyeOpen = false;
                } else if (ear >= EAR_THRESHOLD && !eyeOpen) {
                    eyeOpen = true;
                    blinkState.blinkCount++;
                    updateBlinkUI(blinkState.blinkCount);

                    if (blinkState.blinkCount >= BLINKS_NEEDED) {
                        isProcessing = true;
                        blinkIndicator.classList.add('hidden');
                        setStatus('⏳ Menyimpan check-out...', '', 'blue');
                        await submitCheckOut(det, therapistInfo);
                    }
                }
            }, 200);

            function updateBlinkUI(count) {
                blinkCountEl.textContent = count;
                blinkBar.style.width = Math.min((count / BLINKS_NEEDED) * 100, 100) + '%';
            }

            async function submitCheckOut(det, therapistInfo) {
                try {
                    const snap = document.createElement('canvas');
                    snap.width = video.videoWidth;
                    snap.height = video.videoHeight;
                    snap.getContext('2d').drawImage(video, 0, 0);
                    const blob = await new Promise(r => snap.toBlob(r, 'image/jpeg', 0.9));

                    const formData = new FormData();
                    formData.append('_token', CSRF_TOKEN);
                    formData.append('therapist_id', therapistInfo.id);
                    formData.append('confidence', (1 - det.detection.score).toFixed(4));
                    formData.append('image', blob, 'checkout.jpg');

                    const res = await fetch(CHECKOUT_URL, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if (data.success) {
                        document.getElementById('successName').textContent = '✅ ' + therapistInfo.name;
                        document.getElementById('successTime').textContent = 'Jam keluar: ' + data.time;
                        document.getElementById('workDuration').textContent = '⏱ Durasi kerja: ' + data
                            .duration;
                        successOverlay.classList.remove('hidden');
                        updateTableRowCheckout(therapistInfo.id, data.time, data.duration);
                        setStatus('✅ Check-out berhasil!', therapistInfo.name + ' — ' + data.time, 'green');
                    } else {
                        document.getElementById('errorMsg').textContent = data.message;
                        errorOverlay.classList.remove('hidden');
                        setStatus('❌ ' + data.message, 'Silakan coba lagi', 'red');
                    }
                } catch (e) {
                    document.getElementById('errorMsg').textContent = 'Gagal: ' + e.message;
                    errorOverlay.classList.remove('hidden');
                }

                setTimeout(() => {
                    successOverlay.classList.add('hidden');
                    errorOverlay.classList.add('hidden');
                    blinkState = null;
                    blinkProgressBox.classList.add('hidden');
                    blinkIndicator.classList.add('hidden');
                    updateBlinkUI(0);
                    isProcessing = false;
                    setStatus('👁️ Siap mendeteksi wajah', 'Silakan berdiri di depan kamera',
                        'blue');
                }, 4000);
            }

            function updateTableRowCheckout(id, time, duration) {
                const el = document.getElementById('checkout-' + id);
                if (el) el.innerHTML = '<span class="text-green-600 font-semibold">' + time + '</span>';
                const dur = document.getElementById('duration-' + id);
                if (dur) dur.textContent = duration;
            }

            function setStatus(text, sub, color) {
                const colors = {
                    blue: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200',
                    green: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200',
                    yellow: 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200',
                    red: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200',
                };
                document.getElementById('statusBar').className = 'mb-4 p-4 border rounded-lg text-center ' + (
                    colors[color] || colors.blue);
                statusText.textContent = text;
                statusSub.textContent = sub;
            }
        });
    </script>
</x-app-layout>
