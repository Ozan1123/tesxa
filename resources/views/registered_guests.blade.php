<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Tamu - Devacto FaceID</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/core.css') }}">

    <style>
        body {
            background-color: #f1f5f9;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            height: 64px;
            background: white;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 50;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            background: var(--brand-accent);
            color: white;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem;
        }

        /* Card with Green/Gold Theme */
        .scanner-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 1rem;
            width: 100%;
            max-width: 800px;
            border: 2px solid #10b981;
            /* Green-500 */
            position: relative;
            overflow: hidden;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .scanner-card.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
        }

        .scanner-card.success {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }

        .video-wrapper {
            width: 100%;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #000;
            aspect-ratio: 4/3;
            position: relative;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Scanning Animation */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #10b981;
            box-shadow: 0 0 15px #10b981, 0 0 30px #10b981;
            animation: scan 2s linear infinite;
            z-index: 10;
            display: none;
        }

        @keyframes scan {
            0% {
                top: 0;
                opacity: 0.8;
            }

            100% {
                top: 100%;
                opacity: 0.8;
            }
        }

        /* Info Badge */
        .info-badge {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 20;
            white-space: nowrap;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Identity Overlay */
        .identity-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 30;
            text-align: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .identity-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .id-card {
            background: white;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-xl);
            max-width: 400px;
            width: 90%;
            transform: translateY(20px);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .identity-overlay.show .id-card {
            transform: translateY(0);
        }

        .user-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #10b981;
            margin-bottom: 1rem;
        }

        .user-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-gray-900);
            margin-bottom: 0.25rem;
        }

        .user-role {
            color: var(--color-gray-500);
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .timestamp-badge {
            background: #ecfdf5;
            color: #059669;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        /* Not Found Overlay */
        .error-overlay {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #ef4444;
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            font-weight: 600;
            display: none;
            z-index: 40;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translate(-50%, 20px);
            }

            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="brand">
            <div class="brand-logo" style="background: #10b981;">DF</div>
            <div>
                <h1 style="font-size: 1rem; font-weight: 600;">Devacto FaceID</h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Verifikasi Tamu</p>
            </div>
        </div>
        <div>
            <a href="/" class="btn btn-secondary">Kembali ke Menu</a>
        </div>
    </nav>

    <main class="main-container">
        <div class="scanner-card" id="scanner-frame">
            <div class="info-badge" id="status-badge">
                <div class="spinner" id="status-spinner"></div>
                <span id="status-text">Memuat Database Wajah...</span>
            </div>

            <div class="video-wrapper">
                <video id="video" autoplay muted playsinline></video>
                <div class="scan-line" id="scan-line"></div>
                <!-- Canvas injected here -->
            </div>

            <!-- Identity Found Overlay -->
            <div class="identity-overlay" id="identity-overlay">
                <div class="id-card">
                    <img id="id-photo" src="" alt="User" class="user-photo">
                    <h2 id="id-name" class="user-name">Nama Tamu</h2>
                    <p id="id-purpose" class="user-role">Tujuan Kunjungan</p>
                    <div class="timestamp-badge" id="id-time">Masuk: 10:30 WIB</div>

                    <button class="btn btn-primary" onclick="resetScanner()"
                        style="width: 100%; justify-content: center; background: #10b981; border: none;">
                        Konfirmasi / Tutup
                    </button>
                </div>
            </div>

            <!-- Not Found Alert -->
            <div class="error-overlay" id="error-alert">
                <span>Wajah tidak dikenali dalam sistem.</span>
                <a href="/" style="color: white; text-decoration: underline; margin-left: 8px;">Daftar Tamu Baru</a>
            </div>
        </div>
    </main>

    <!-- Audio -->
    <audio id="audio-ding" src="{{ asset('audio/success-fanfare-trumpets-6185.mp3') }}" preload="auto"></audio>

    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        // --- Configuration ---
        const CONFIDENCE_THRESHOLD = 0.5;

        // --- State ---
        let faceMatcher = null;
        let isModelLoaded = false;
        let isPROCESSING = false; // Debounce
        let guestsData = [];

        // --- Elements ---
        const video = document.getElementById('video');
        const scannerFrame = document.getElementById('scanner-frame');
        const statusText = document.getElementById('status-text');
        const statusSpinner = document.getElementById('status-spinner');
        const scanLine = document.getElementById('scan-line');
        const identityOverlay = document.getElementById('identity-overlay');
        const errorAlert = document.getElementById('error-alert');
        const audioDing = document.getElementById('audio-ding');

        // --- 1. Initialization Flow ---
        (async () => {
            try {
                // Load Models
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);

                statusText.innerText = "Mengunduh Database Wajah...";

                // Fetch Guests Data
                const response = await fetch("{{ route('api.guests') }}");
                guestsData = await response.json();

                statusText.innerText = `Memproses ${guestsData.length} Data Wajah...`;

                // Create Face Matcher
                faceMatcher = await createFaceMatcher(guestsData);

                if (faceMatcher) {
                    statusText.innerText = "Sistem Siap. Scanning...";
                    statusSpinner.style.display = 'none';
                    isModelLoaded = true;
                    scanLine.style.display = 'block';
                    startVideo();
                } else {
                    statusText.innerText = "Database Kosong / Gagal.";
                    statusSpinner.style.display = 'none';
                    // Still start video to show user, but matching won't work well
                    startVideo();
                }

            } catch (err) {
                console.error(err);
                statusText.innerText = "Error System Inisialisasi: " + err.message;
            }
        })();

        // --- 2. Create Face Matcher ---
        async function createFaceMatcher(guests) {
            const labeledFaceDescriptors = [];

            for (const guest of guests) {
                try {
                    const img = await faceapi.fetchImage(guest.photo_url);
                    const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor(); // Added TinyFaceDetectorOptions

                    if (detection) {
                        // Use name + unique ID logic if needed, here simple name map
                        // We attach the full guest object to the label via a lookup map or just assume unique names
                        // For simplicity, we use Name as label. In real app, use ID.
                        labeledFaceDescriptors.push(new faceapi.LabeledFaceDescriptors(guest.name, [detection.descriptor]));
                    }
                } catch (e) {
                    console.log("Error loading guest image:", guest.name); // Added console log for failed images
                }
            }

            if (labeledFaceDescriptors.length === 0) return null;
            return new faceapi.FaceMatcher(labeledFaceDescriptors, CONFIDENCE_THRESHOLD);
        }

        // --- 3. Start Camera ---
        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => { video.srcObject = stream; })
                .catch(err => console.error(err));
        }

        // --- 4. Detection Loop ---
        video.addEventListener('play', () => {
            const canvas = faceapi.createCanvasFromMedia(video);
            document.querySelector('.video-wrapper').append(canvas);
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);

            setInterval(async () => {
                if (!isModelLoaded || isPROCESSING || !faceMatcher) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors(); // Added TinyFaceDetectorOptions
                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detections.length > 0) {
                    // Try to match
                    const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

                    results.forEach((result, i) => {
                        const box = resizedDetections[i].detection.box;

                        if (result.label !== 'unknown') {
                            // MATCH FOUND
                            handleMatchFound(result.label);

                            const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString(), boxColor: '#10b981' });
                            drawBox.draw(canvas);
                        } else {
                            // NO MATCH
                            scannerFrame.classList.add('error');
                            errorAlert.style.display = 'block';
                            setTimeout(() => {
                                scannerFrame.classList.remove('error');
                                errorAlert.style.display = 'none';
                            }, 2000);

                            const drawBox = new faceapi.draw.DrawBox(box, { label: "Tidak Dikenal", boxColor: '#ef4444' });
                            drawBox.draw(canvas);
                        }
                    });
                }
            }, 500); // Check every 500ms
        });

        // --- 5. Handlers ---
        async function handleMatchFound(name) {
            isPROCESSING = true; // Pause detection

            // Find guest data
            const guest = guestsData.find(g => g.name === name);
            if (!guest) { isPROCESSING = false; return; }

            // 1. Visuals
            scannerFrame.classList.add('success');
            scanLine.style.display = 'none';

            // 2. Populate Overlay
            document.getElementById('id-photo').src = guest.photo_url;
            document.getElementById('id-name').innerText = guest.name;
            document.getElementById('id-purpose').innerText = guest.purpose;
            document.getElementById('id-time').innerText = "Terdaftar: " + guest.date + " " + guest.registered_at;

            // 3. Backend Check-in (Auto Log Visit)
            try {
                await fetch("{{ route('visits.checkin') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // Need to ensure meta csrf exists in head, usually does in Laravel layouts
                    },
                    body: JSON.stringify({ guest_id: guest.id })
                });
                console.log("Visit Logged:", guest.name);
            } catch (e) {
                console.error("Check-in Failed", e);
            }

            // 4. Show Overlay
            identityOverlay.classList.add('show');
            audioDing.play().catch(() => { });

            // 5. TTS
            speak(`Halo ${guest.name}, selamat datang.`);
        }

        function resetScanner() {
            identityOverlay.classList.remove('show');
            scannerFrame.classList.remove('success');
            scanLine.style.display = 'block';
            setTimeout(() => { isPROCESSING = false; }, 1000);
        }

        function speak(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                window.speechSynthesis.speak(utterance);
            }
        }
    </script>
</body>

</html>
