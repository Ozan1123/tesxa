<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DEVACTO FACEID // SYSTEM ONLINE</title>
    <style>
        /* ============================================
           GLOBAL STYLES
        ============================================ */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            background-color: #000;
            color: #00ff00;
            font-family: 'Courier New', Courier, monospace;
            overflow: hidden;
        }

        /* ============================================
           LOADING OVERLAY
        ============================================ */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* ============================================
           FLASH OVERLAY (Camera Shutter Effect)
        ============================================ */
        #flash-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 90;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.1s ease-out;
        }

        /* ============================================
           APP LAYOUT (Split Screen)
        ============================================ */
        #app-layout {
            display: flex;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            position: relative;
            background: #000;
        }

        /* ============================================
           CAMERA SECTION (Left Panel)
        ============================================ */
        .camera-section {
            flex: 1;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            background: #000;
            transition: all 0.5s cubic-bezier(0.4, 0.0, 0.2, 1);
        }

        /* When form is active, slide camera to the left */
        .form-active .camera-section {
            justify-content: flex-start;
            padding-left: 3%;
        }

        /* Camera Container - ALWAYS has Hacker Border */
        .main-container {
            position: relative;
            border: 3px solid #00ff00;
            box-shadow: 0 0 30px #00ff00, inset 0 0 10px rgba(0, 255, 0, 0.1);
            background: #000;
        }

        video {
            display: block;
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        /* ============================================
           STATUS INDICATOR (Top of Camera)
        ============================================ */
        #status-indicator {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            padding: 8px 20px;
            border: 2px solid #00ff00;
            color: #00ff00;
            font-weight: bold;
            z-index: 10;
            letter-spacing: 2px;
            font-size: 14px;
            white-space: nowrap;
            transition: all 0.3s;
        }

        /* ============================================
           SCAN BUTTON (Bottom of Camera)
        ============================================ */
        #scan-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            text-align: center;
            transition: opacity 0.3s;
        }

        .btn-hacker {
            background: #000;
            border: 2px solid #00ff00;
            color: #00ff00;
            padding: 15px 40px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            box-shadow: 0 0 15px #00ff00;
            transition: all 0.3s;
            letter-spacing: 2px;
        }

        .btn-hacker:hover:not(:disabled) {
            background: #00ff00;
            color: #000;
            box-shadow: 0 0 30px #00ff00;
        }

        .btn-hacker:disabled {
            border-color: #555;
            color: #555;
            box-shadow: none;
            cursor: not-allowed;
            background: #111;
        }

        /* Hide controls when form is active */
        .form-active #scan-controls,
        .form-active #status-indicator {
            opacity: 0;
            pointer-events: none;
        }

        /* ============================================
           SIDE PANEL (Right - Form)
        ============================================ */
        #side-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 400px;
            max-width: 40%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            border-left: 3px solid #00ff00;
            box-shadow: -10px 0 40px rgba(0, 255, 0, 0.3);
            z-index: 50;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
        }

        .form-active #side-panel {
            transform: translateX(0);
        }

        .terminal-header {
            font-size: 20px;
            margin-bottom: 25px;
            text-shadow: 0 0 10px #00ff00;
            border-bottom: 1px solid #00ff00;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            color: #00ff00;
        }

        .form-input {
            width: 100%;
            background: #001100;
            border: 1px solid #00ff00;
            color: #00ff00;
            padding: 12px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            box-shadow: 0 0 10px #00ff00;
            background: #002200;
        }

        .hidden-field {
            display: none;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .form-actions .btn-hacker {
            flex: 1;
            padding: 12px 20px;
            font-size: 14px;
        }

        .validation-warning {
            color: #ff4444;
            font-size: 12px;
            text-align: center;
            padding: 10px;
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff4444;
            display: none;
            margin-bottom: 15px;
        }

        /* ============================================
           SUCCESS OVERLAY (CENTER POPUP)
        ============================================ */
        #success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 200;
            display: none;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .success-content {
            background: #000;
            border: 3px solid #00ff00;
            box-shadow: 0 0 50px #00ff00;
            padding: 60px 80px;
            animation: pulse-glow 1s infinite alternate;
        }

        @keyframes pulse-glow {
            from {
                box-shadow: 0 0 30px #00ff00;
            }

            to {
                box-shadow: 0 0 60px #00ff00, 0 0 100px rgba(0, 255, 0, 0.5);
            }
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .success-title {
            font-size: 36px;
            font-weight: bold;
            color: #00ff00;
            text-shadow: 0 0 20px #00ff00;
            margin-bottom: 10px;
        }

        .success-subtitle {
            font-size: 18px;
            color: #00ff00;
            margin-bottom: 20px;
        }

        .success-time {
            font-size: 24px;
            color: #fff;
            margin-bottom: 30px;
        }

        .reset-timer {
            font-size: 14px;
            color: #888;
            animation: blinker 0.5s linear infinite alternate;
        }

        /* ============================================
           ANIMATIONS
        ============================================ */
        .blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
    </style>
</head>

<body>

    <!-- 1. LOADING OVERLAY -->
    <div id="loading-overlay">
        <h1 style="text-shadow: 0 0 20px #00ff00;">INITIALIZING NEURAL NETWORKS...</h1>
        <p class="blink">[ LOADING FACE DETECTION MODELS ]</p>
    </div>

    <!-- 2. FLASH OVERLAY -->
    <div id="flash-overlay"></div>

    <!-- AUDIO ELEMENTS (Hidden, no controls) -->
    <audio id="audio-shutter" src="{{ asset('audio/camera-shutter-and-flash-combined-6827.mp3') }}" preload="auto"
        style="display:none;"></audio>
    <audio id="audio-success" src="{{ asset('audio/success-fanfare-trumpets-6185.mp3') }}" preload="auto"
        style="display:none;"></audio>

    <!-- 3. APP LAYOUT -->
    <div id="app-layout">

        <!-- CAMERA SECTION -->
        <div class="camera-section">
            <div class="main-container" id="camera-container">
                <video id="video" width="720" height="560" autoplay muted playsinline></video>
                <!-- Canvas will be appended by JS -->

                <div id="status-indicator">[ INITIALIZING... ]</div>

                <div id="scan-controls">
                    <button class="btn-hacker" id="btn-scan" disabled>[ SCAN WAJAH & MASUK ]</button>
                </div>
            </div>
        </div>

        <!-- SIDE PANEL (FORM) -->
        <div id="side-panel">
            <div class="terminal-header">>> GUEST_REGISTRATION.EXE</div>

            <form id="guest-form">
                <input type="hidden" id="image-data" name="image">

                <div class="form-group">
                    <label class="form-label" style="color:#666;">GENDER (AI DETECTED):</label>
                    <select class="form-input" id="gender" name="gender" style="cursor:not-allowed; opacity:0.7;"
                        tabindex="-1">
                        <option value="male">MALE</option>
                        <option value="female">FEMALE</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">NAMA TAMU:</label>
                    <input type="text" class="form-input" id="name" name="name" required autocomplete="off"
                        placeholder="Ketik nama anda...">
                </div>

                <div class="form-group">
                    <label class="form-label">JENIS TAMU:</label>
                    <select class="form-input" id="guest_type" name="guest_type" required>
                        <option value="Tamu Umum">Tamu Umum</option>
                        <option value="Orang Tua">Orang Tua</option>
                        <option value="Dinas">Dinas</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">TUJUAN:</label>
                    <select class="form-input" id="purpose" name="purpose" required>
                        <option value="Lihat Pameran">Lihat Pameran</option>
                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                        <option value="Wali Kelas">Wali Kelas</option>
                        <option value="TU">TU</option>
                    </select>
                </div>

                <div class="form-group hidden-field" id="class-info-group">
                    <label class="form-label">INFO KELAS / SISWA:</label>
                    <input type="text" class="form-input" id="class_info" name="class_info"
                        placeholder="Nama Siswa / Kelas">
                </div>

                <div class="validation-warning" id="face-warning">
                    ⚠️ WAJAH TIDAK TERDETEKSI - LIHAT KE KAMERA
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-hacker" id="btn-cancel"
                        style="background:#300; border-color:#f00; color:#f00;">BATAL</button>
                    <button type="submit" class="btn-hacker" id="btn-submit">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. SUCCESS OVERLAY (CENTER POPUP) -->
    <div id="success-overlay">
        <div class="success-content">
            <div class="success-icon">✅</div>
            <div class="success-title">ACCESS GRANTED</div>
            <div class="success-subtitle">TAMU BERHASIL TERCATAT</div>
            <div class="success-time">JAM MASUK: <span id="success-time">00:00</span></div>
            <div class="reset-timer">Kembali dalam <span id="countdown">3</span>...</div>
        </div>
    </div>

    <!-- Load Face API -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        // ============================================
        // DOM ELEMENTS
        // ============================================
        const video = document.getElementById('video');
        const loadingOverlay = document.getElementById('loading-overlay');
        const flashOverlay = document.getElementById('flash-overlay');
        const cameraContainer = document.getElementById('camera-container');
        const statusIndicator = document.getElementById('status-indicator');
        const successOverlay = document.getElementById('success-overlay');
        const faceWarning = document.getElementById('face-warning');

        const btnScan = document.getElementById('btn-scan');
        const btnCancel = document.getElementById('btn-cancel');
        const btnSubmit = document.getElementById('btn-submit');
        const guestForm = document.getElementById('guest-form');
        const purposeSelect = document.getElementById('purpose');
        const classInfoGroup = document.getElementById('class-info-group');
        const genderSelect = document.getElementById('gender');

        // Audio Elements
        const audioShutter = document.getElementById('audio-shutter');
        const audioSuccess = document.getElementById('audio-success');

        // ============================================
        // STATE VARIABLES
        // ============================================
        let detectionInterval = null;
        let canvas = null;
        let currentGender = 'male';
        let isFacePresent = false;
        let isModalOpen = false;

        // ============================================
        // AUDIO HELPER (Use actual audio files)
        // ============================================
        function playSound(type) {
            try {
                if (type === 'shutter' && audioShutter) {
                    audioShutter.currentTime = 0;
                    audioShutter.play().catch(e => console.log("Audio blocked:", e));
                } else if (type === 'success' && audioSuccess) {
                    audioSuccess.currentTime = 0;
                    audioSuccess.play().catch(e => console.log("Audio blocked:", e));
                }
            } catch (e) {
                console.warn("Audio Error:", e);
            }
        }

        // ============================================
        // STEP 1: LOAD MODELS
        // ============================================
        console.log("🚀 Loading Face API Models...");

        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
            faceapi.nets.faceExpressionNet.loadFromUri('/models'),
            faceapi.nets.ageGenderNet.loadFromUri('/models')
        ]).then(() => {
            console.log("✅ Models Loaded!");
            startVideo();
        }).catch(err => {
            console.error("❌ Model Loading Failed:", err);
            alert("SYSTEM ERROR: Failed to load AI models. Please refresh.");
        });

        // ============================================
        // STEP 2: START CAMERA
        // ============================================
        function startVideo() {
            console.log("📷 Initializing Camera...");

            // Security Check
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                console.warn("⚠️ Running on HTTP - Camera may not work on some browsers");
            }

            // API Support Check
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("CRITICAL ERROR: Camera API not supported. Use Chrome/Firefox.");
                return;
            }

            // Request Camera
            navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 720 },
                    height: { ideal: 560 },
                    facingMode: 'user'
                }
            })
                .then(stream => {
                    console.log("✅ Camera Access Granted!");
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error("❌ Camera Error:", err);
                    loadingOverlay.innerHTML = `
                    <h1 style="color:#ff0000;">CAMERA ERROR</h1>
                    <p style="color:#ff6666;">${err.name}: ${err.message}</p>
                    <p style="margin-top:20px;">Please allow camera access and refresh.</p>
                `;
                });
        }

        // ============================================
        // STEP 3: DETECTION LOOP
        // ============================================
        video.addEventListener('loadeddata', () => {
            console.log("📹 Video Data Loaded - Starting Detection...");

            // Safety Check
            if (video.readyState < 2) {
                console.warn("Video not ready, waiting...");
                return;
            }

            // Hide Loading
            loadingOverlay.style.display = 'none';

            // Create Canvas (once)
            if (!canvas) {
                try {
                    canvas = faceapi.createCanvasFromMedia(video);
                    cameraContainer.appendChild(canvas);
                    console.log("✅ Canvas Created");
                } catch (err) {
                    console.error("❌ Canvas Error:", err);
                    return;
                }
            }

            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            // Clear existing interval
            if (detectionInterval) clearInterval(detectionInterval);

            // Start Detection Loop
            detectionInterval = setInterval(async () => {
                if (video.paused || video.ended || video.readyState < 2) return;

                try {
                    const detections = await faceapi
                        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceExpressions()
                        .withAgeAndGender();

                    const resized = faceapi.resizeResults(detections, displaySize);
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    isFacePresent = detections.length > 0;

                    // Update Status (only when form is closed)
                    if (!isModalOpen) {
                        if (isFacePresent) {
                            statusIndicator.innerText = "[ SUBJECT IDENTIFIED: READY TO SCAN ]";
                            statusIndicator.style.color = "#00ff00";
                            statusIndicator.style.borderColor = "#00ff00";
                            statusIndicator.style.background = "rgba(0, 50, 0, 0.8)";
                            btnScan.disabled = false;
                        } else {
                            statusIndicator.innerText = "[ NO FACE DETECTED - LOOK AT CAMERA ]";
                            statusIndicator.style.color = "#ff4444";
                            statusIndicator.style.borderColor = "#ff4444";
                            statusIndicator.style.background = "rgba(50, 0, 0, 0.8)";
                            btnScan.disabled = true;
                        }
                    }

                    // Draw Detections
                    if (isFacePresent) {
                        currentGender = detections[0].gender;

                        if (!isModalOpen) {
                            faceapi.draw.drawDetections(canvas, resized, {
                                boxColor: '#00ff00',
                                lineWidth: 2
                            });

                            resized.forEach(d => {
                                const box = d.detection.box;
                                const label = `${d.gender.toUpperCase()} [${Math.round(d.age)}]`;
                                ctx.font = 'bold 14px "Courier New"';
                                ctx.fillStyle = '#00ff00';
                                ctx.fillText(label, box.x, box.bottom + 18);
                            });
                        }
                    }

                    // Live Validation (when form is open)
                    if (isModalOpen) {
                        if (isFacePresent) {
                            btnSubmit.disabled = false;
                            faceWarning.style.display = 'none';
                        } else {
                            btnSubmit.disabled = true;
                            faceWarning.style.display = 'block';
                        }
                    }

                } catch (err) {
                    // Silent fail - just skip this frame
                }
            }, 150);
        });

        // ============================================
        // STEP 4: SCAN BUTTON HANDLER
        // ============================================
        btnScan.addEventListener('click', () => {
            // Flash Effect
            flashOverlay.style.opacity = 0.8;
            playSound('shutter');
            setTimeout(() => { flashOverlay.style.opacity = 0; }, 100);

            // Capture Image
            const captureCanvas = document.createElement('canvas');
            captureCanvas.width = video.videoWidth;
            captureCanvas.height = video.videoHeight;
            captureCanvas.getContext('2d').drawImage(video, 0, 0);
            document.getElementById('image-data').value = captureCanvas.toDataURL('image/png');

            // Set Gender
            genderSelect.value = currentGender;

            // Show Form Panel
            document.body.classList.add('form-active');
            isModalOpen = true;

            // Focus Name Input
            setTimeout(() => {
                document.getElementById('name').focus();
            }, 400);
        });

        // ============================================
        // STEP 5: FORM HANDLERS
        // ============================================
        purposeSelect.addEventListener('change', (e) => {
            if (e.target.value === 'Wali Kelas') {
                classInfoGroup.classList.remove('hidden-field');
                document.getElementById('class_info').required = true;
            } else {
                classInfoGroup.classList.add('hidden-field');
                document.getElementById('class_info').required = false;
            }
        });

        btnCancel.addEventListener('click', resetInterface);

        guestForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!isFacePresent) {
                alert("DENIED: No face detected!");
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerText = "SAVING...";

            const formData = new FormData(guestForm);

            try {
                const response = await fetch("{{ route('guest.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showSuccess();
                } else {
                    alert("ERROR: " + (result.message || "Unknown error"));
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = "SIMPAN";
                }
            } catch (error) {
                console.error("Submit Error:", error);
                alert("SERVER ERROR: Please try again.");
                btnSubmit.disabled = false;
                btnSubmit.innerText = "SIMPAN";
            }
        });

        // ============================================
        // STEP 6: SUCCESS HANDLER
        // ============================================
        function showSuccess() {
            // Hide form panel
            document.body.classList.remove('form-active');
            isModalOpen = false;

            // Show success overlay
            successOverlay.style.display = 'flex';
            playSound('success');

            // Set time
            const now = new Date();
            document.getElementById('success-time').innerText =
                now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0');

            // Countdown
            let seconds = 3;
            const countdownEl = document.getElementById('countdown');
            const timer = setInterval(() => {
                seconds--;
                countdownEl.innerText = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.reload();
                }
            }, 1000);
        }

        function resetInterface() {
            successOverlay.style.display = 'none';
            document.body.classList.remove('form-active');
            isModalOpen = false;
            guestForm.reset();
            classInfoGroup.classList.add('hidden-field');
            btnSubmit.disabled = false;
            btnSubmit.innerText = "SIMPAN";
        }

        // ============================================
        // SECRET ADMIN SHORTCUT (Ctrl+Shift+U)
        // ============================================
        document.addEventListener('keydown', function (e) {
            // Check for Ctrl + Shift + U
            if (e.ctrlKey && e.shiftKey && (e.key === 'U' || e.key === 'u')) {
                e.preventDefault(); // Prevent browser "View Source" or other defaults
                console.log("🔐 Admin Shortcut Activated!");
                window.location.href = '/admin';
            }
        });
    </script>
</body>

</html>