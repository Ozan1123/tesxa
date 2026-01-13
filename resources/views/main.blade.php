<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Devacto FaceID - Secure Access</title>

    <!-- Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/core.css') }}">

    <style>
        /* Specific Styles for Scanner Page */
        body {
            background-color: #f1f5f9;
            /* Slate 100 */
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

        .scanner-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 1rem;
            width: 100%;
            max-width: 1000px;
            display: flex;
            gap: 2rem;
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .video-wrapper {
            flex: 1;
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #000;
            aspect-ratio: 4/3;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
            /* Mirror effect */
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }

        .scanner-sidebar {
            width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1rem;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--color-gray-100);
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: var(--color-gray-600);
            width: fit-content;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-gray-400);
        }

        .status-dot.active {
            background: var(--color-success);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .status-dot.error {
            background: var(--color-error);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.625rem;
            border: 1px solid var(--color-gray-300);
            border-radius: var(--radius-md);
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--brand-accent);
            ring: 2px solid var(--brand-accent/20);
        }

        .hidden {
            display: none !important;
        }

        /* Overlays */
        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 20;
            text-align: center;
        }

        #start-overlay {
            z-index: 100;
        }

        .success-animation {
            width: 80px;
            height: 80px;
            background: var(--color-success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-bottom: 1rem;
            animation: pulse-ring 2s infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .scanner-card {
                flex-direction: column;
                max-width: 500px;
            }

            .scanner-sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Start Overlay (Required for Audio Context) -->
    <div id="start-overlay" class="overlay">
        <div class="brand-logo" style="width: 64px; height: 64px; font-size: 24px; margin-bottom: 1.5rem;">DF</div>
        <h1 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Selamat Datang di Devacto FaceID</h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Sistem Registrasi Tamu Terintegrasi AI</p>
        <div style="display: flex; gap: 1rem;">
            <button id="btn-start" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                Mulai Sistem
            </button>
            <a href="{{ route('registered.guests') }}" class="btn btn-secondary"
                style="padding: 0.75rem 2rem; text-decoration: none;">
                Tamu Terdaftar
            </a>
        </div>
    </div>

    <!-- Success Overlay -->
    <div id="success-overlay" class="overlay hidden">
        <div class="success-animation">✓</div>
        <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Registrasi Berhasil</h2>
        <p style="color: var(--text-muted);">Terima kasih, silakan masuk.</p>
        <p id="countdown" style="margin-top: 1rem; color: var(--brand-accent); font-weight: 500;">Reloading...</p>
    </div>

    <nav class="navbar">
        <div class="brand">
            <div class="brand-logo">DF</div>
            <div>
                <h1 style="font-size: 1rem; font-weight: 600;">Devacto FaceID</h1>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Enterprise Guest Management</p>
            </div>
        </div>
        <div>
            <a href="/admin" class="btn btn-secondary">Dashboard Admin</a>
        </div>
    </nav>

    <main class="main-container">
        <div class="scanner-card">

            <!-- Video Area -->
            <div class="video-wrapper" id="camera-container">
                <video id="video" autoplay muted playsinline></video>
                <!-- Canvas injected here -->
            </div>

            <!-- Sidebar Form -->
            <div class="scanner-sidebar">
                <div class="status-indicator">
                    <div id="status-dot" class="status-dot"></div>
                    <span id="status-text">Menunggu Kamera...</span>
                </div>

                <form id="guest-form">
                    <input type="hidden" name="gender" id="gender">
                    <input type="hidden" name="image" id="image-data">
                    <!-- Face Descriptor Hidden Input -->
                    <input type="hidden" name="face_descriptor" id="face_descriptor">

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" id="name" name="name" required
                            placeholder="Masukkan nama...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tujuan Kunjungan</label>
                        <select class="form-input" id="purpose" name="purpose">
                            <option value="Dinas">Dinas</option>
                            <option value="Orang Tua">Orang Tua / Wali</option>
                            <option value="Tamu Umum">Tamu Umum</option>
                            <option value="Alumni">Alumni</option>
                            <option value="Wali Kelas">Wali Kelas</option>
                        </select>
                    </div>

                    <!-- Hidden Info Field for Wali Kelas -->
                    <div class="form-group hidden" id="class-info-group">
                        <label class="form-label">Info Kelas / Siswa</label>
                        <input type="text" class="form-input" id="class_info" name="class_info"
                            placeholder="Nama Siswa / Kelas">
                    </div>

                    <div class="form-group" id="guest-type-group">
                        <label class="form-label">Status Tamu</label>
                        <select class="form-input" id="guest_type" name="guest_type">
                            <option value="Tamu Umum">Tamu Umum</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Dinas">Dinas</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>

                    <button type="submit" id="btn-submit" class="btn btn-primary"
                        style="width: 100%; justify-content: center;" disabled>
                        Simpan Data
                    </button>

                    <p id="face-warning"
                        style="font-size: 0.75rem; color: var(--color-error); text-align: center; margin-top: 1rem; display: none;">
                        Wajah tidak terdeteksi. Harap menghadap kamera.
                    </p>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal Check-in -->
    <div id="modal-checkin" class="overlay hidden" style="z-index: 60;">
        <div class="scanner-card" style="max-width: 400px; flex-direction: column; text-align: center;">
            <div class="success-animation" style="margin: 0 auto 1rem;">👋</div>
            <h2 style="font-size: 1.5rem; font-weight: 600;">Selamat Datang</h2>
            <h3 id="checkin-name" style="color: var(--brand-primary); margin-bottom: 1rem;">[Nama Tamu]</h3>

            <p style="margin-bottom: 1rem; color: var(--text-muted);">Silakan isi tujuan kunjungan Anda hari ini.</p>

            <input type="text" id="checkin-purpose" class="form-input" placeholder="Contoh: Bertemu Kepala Sekolah" style="margin-bottom: 1rem;">

            <div style="display: flex; gap: 1rem; width: 100%;">
                <button onclick="closeModal('modal-checkin')" class="btn btn-secondary" style="flex: 1; justify-content: center;">Batal</button>
                <button id="btn-confirm-checkin" class="btn btn-primary" style="flex: 1; justify-content: center;">Check In</button>
            </div>
        </div>
    </div>

    <!-- Modal Check-out -->
    <div id="modal-checkout" class="overlay hidden" style="z-index: 60;">
        <div class="scanner-card" style="max-width: 400px; flex-direction: column; text-align: center;">
            <div class="success-animation" style="margin: 0 auto 1rem; background: #ef4444;">👋</div>
            <h2 style="font-size: 1.5rem; font-weight: 600;">Konfirmasi Pulang</h2>
            <h3 id="checkout-name" style="color: var(--brand-primary); margin-bottom: 1rem;">[Nama Tamu]</h3>

            <p style="margin-bottom: 1rem; color: var(--text-muted);">Apakah Anda ingin menyelesaikan kunjungan?</p>

            <div style="display: flex; gap: 1rem; width: 100%;">
                <button onclick="closeModal('modal-checkout')" class="btn btn-secondary" style="flex: 1; justify-content: center;">Batal</button>
                <button id="btn-confirm-checkout" class="btn btn-primary" style="flex: 1; justify-content: center; background: #ef4444; border-color: #ef4444;">Ya, Checkout</button>
            </div>
        </div>
    </div>

    <!-- Audio -->
    <audio id="audio-success" src="{{ asset('audio/success-fanfare-trumpets-6185.mp3') }}" preload="auto"></audio>

    <!-- Scripts -->
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        // State
        let isModelLoaded = false;
        let isFaceDetected = false;
        let detectedGender = 'male';
        let faceMatcher = null;
        let isProcessing = false;
        let currentBestDescriptor = null; // Store for registration
        let guestsMap = {}; // ID -> Guest Object

        // Elements
        const video = document.getElementById('video');
        const canvasContainer = document.getElementById('camera-container');
        const startOverlay = document.getElementById('start-overlay');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const btnSubmit = document.getElementById('btn-submit');
        const faceWarning = document.getElementById('face-warning');
        const audioSuccess = document.getElementById('audio-success');

        // Form Elements
        const guestForm = document.getElementById('guest-form');
        const purposeSelect = document.getElementById('purpose');
        const classInfoGroup = document.getElementById('class-info-group');
        const classInfoInput = document.getElementById('class_info');

        // Modals
        const modalCheckin = document.getElementById('modal-checkin');
        const modalCheckout = document.getElementById('modal-checkout');

        // 1. Initial Start
        document.getElementById('btn-start').addEventListener('click', async () => {
            startOverlay.style.display = 'none';
            playDing();
            await loadModels();
        });

        function playDing() {
            audioSuccess.play().then(() => {
                audioSuccess.pause();
                audioSuccess.currentTime = 0;
            }).catch(() => {});
        }

        // 2. Load Models & Descriptors
        async function loadModels() {
            statusText.innerText = "Memuat AI Model...";
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.ageGenderNet.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);

                // Fetch Descriptors
                statusText.innerText = "Memuat Data Wajah...";
                const res = await fetch("{{ route('api.descriptors') }}");
                const guests = await res.json();

                if (guests.length > 0) {
                    const labeledDescriptors = guests.map(guest => {
                        guestsMap[guest.id] = guest; // Cache guest info
                        // Parse descriptor string to Float32Array
                        if(guest.face_descriptor) {
                            try {
                                const descriptor = new Float32Array(Object.values(JSON.parse(guest.face_descriptor)));
                                return new faceapi.LabeledFaceDescriptors(guest.id.toString(), [descriptor]);
                            } catch(e) { return null; }
                        }
                        return null;
                    }).filter(d => d !== null);

                    if(labeledDescriptors.length > 0) {
                        faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
                    }
                }

                isModelLoaded = true;
                startVideo();
            } catch (e) {
                console.error("Setup Error:", e);
                statusText.innerText = "Error: " + e.message;
                statusDot.classList.add('error');
            }
        }

        // 3. Start Camera
        function startVideo() {
            statusText.innerText = "Mengakses Kamera...";
            navigator.mediaDevices.getUserMedia({ video: { width: 720, height: 560 } })
                .then(stream => {
                    video.srcObject = stream;
                    statusText.innerText = "Sistem Siap";
                    statusDot.classList.add('active');
                })
                .catch(err => {
                    console.error(err);
                    statusText.innerText = "Kamera Error: " + err.message;
                    statusDot.classList.add('error');
                });
        }

        // 4. Detection Loop
        video.addEventListener('play', () => {
            const canvas = faceapi.createCanvasFromMedia(video);
            canvasContainer.append(canvas);
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);

            setInterval(async () => {
                if (!isModelLoaded || video.paused || video.ended || isProcessing) return;

                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptors()
                    .withAgeAndGender();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detections.length > 0) {
                    const detection = resizedDetections[0]; // Focus on first face
                    isFaceDetected = true;
                    faceWarning.style.display = 'none';
                    detectedGender = detection.gender;
                    currentBestDescriptor = detection.descriptor; // Save for registration

                    // Draw Box
                    const box = detection.detection.box;
                    ctx.strokeStyle = '#3b82f6';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);

                    // Identify
                    if (faceMatcher) {
                        const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                        if (bestMatch.label !== 'unknown') {
                            // MATCH FOUND -> CHECK STATUS
                            const guestId = bestMatch.label;
                            const guest = guestsMap[guestId];
                            if(guest) handleKnownGuest(guest);
                        } else {
                            // UNKNOWN -> SHOW FORM
                            handleUnknownGuest();
                        }
                    } else {
                        // No DB -> Unknown
                        handleUnknownGuest();
                    }
                } else {
                    isFaceDetected = false;
                    faceWarning.style.display = 'block';
                    btnSubmit.disabled = true;
                }
            }, 1000);
        });

        // 5. Logic Handlers
        async function handleKnownGuest(guest) {
            if (isProcessing) return;
            isProcessing = true;
            statusText.innerText = "Identifikasi: " + guest.name;
            statusText.style.color = "var(--brand-accent)";

            // Call Backend to Check Status
            try {
                const res = await fetch("{{ route('visits.checkStatus') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ guest_id: guest.id })
                });
                const data = await res.json();

                if (data.status === 'active') {
                    // CONDITION B: Mau Pulang
                    showCheckoutModal(guest, data.visit);
                } else {
                    // CONDITION A: Baru Masuk
                    showCheckinModal(guest);
                }
            } catch (e) {
                console.error(e);
                isProcessing = false;
            }
        }

        function handleUnknownGuest() {
            // UI Update: Enable Form
            statusText.innerText = "Wajah Tidak Dikenal - Silakan Daftar";
            statusText.style.color = "var(--color-gray-600)";
            btnSubmit.disabled = false;

            // Fill hidden descriptor for registration
            let descInput = document.getElementById('face_descriptor');
            if (currentBestDescriptor && descInput) {
                descInput.value = JSON.stringify(Array.from(currentBestDescriptor));
            }
        }

        // 6. Modal Logic
        function showCheckinModal(guest) {
            document.getElementById('checkin-name').innerText = guest.name;
            modalCheckin.classList.remove('hidden');

            speak(`Selamat datang, ${guest.name}.`);

            const btn = document.getElementById('btn-confirm-checkin');
            btn.onclick = async () => {
                const purpose = document.getElementById('checkin-purpose').value;
                if(!purpose) { alert("Isi tujuan!"); return; }

                btn.innerText = "Processing...";
                await apiCheckIn(guest.id, purpose);
                closeModal('modal-checkin');
                showSuccess(`Selamat Datang, ${guest.name}`);
            };
        }

        function showCheckoutModal(guest, visit) {
            document.getElementById('checkout-name').innerText = guest.name;
            modalCheckout.classList.remove('hidden');

             speak(`Halo ${guest.name}, ingin check out?`);

            const btn = document.getElementById('btn-confirm-checkout');
            btn.onclick = async () => {
                btn.innerText = "Processing...";
                await apiCheckOut(guest.id);
                closeModal('modal-checkout');
                showSuccess(`Sampai Jumpa, ${guest.name}`);
            };
        }

        // Expose close to window
        window.closeModal = function(id) {
            document.getElementById(id).classList.add('hidden');
            isProcessing = false;
            document.getElementById('btn-confirm-checkin').innerText = "Check In";
            document.getElementById('btn-confirm-checkout').innerText = "Ya, Checkout";
        }

        // 7. API Calls
        async function apiCheckIn(guestId, purpose) {
            await fetch("{{ route('visits.checkin') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: guestId, purpose: purpose })
            });
        }

        async function apiCheckOut(guestId) {
            await fetch("{{ route('visits.checkOut') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: guestId })
            });
        }

        // 8. Registration Submit (For Unknown)
        guestForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const captureCanvas = document.createElement('canvas');
            captureCanvas.width = video.videoWidth;
            captureCanvas.height = video.videoHeight;
            captureCanvas.getContext('2d').drawImage(video, 0, 0);
            document.getElementById('image-data').value = captureCanvas.toDataURL('image/png');
            document.getElementById('gender').value = detectedGender;

            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerText = "Menyimpan...";

            try {
                const formData = new FormData(e.target);
                const res = await fetch("{{ route('guest.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();

                if (data.success) {
                    showSuccess("Registrasi Berhasil!");
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
            }
        });

        // Utilities
        function showSuccess(msg) {
            audioSuccess.play();
            const overlay = document.getElementById('success-overlay');
            overlay.innerHTML = `
                <div class="success-animation">✓</div>
                <h2 style="font-size: 1.5rem; font-weight: 600;">Berhasil</h2>
                <p style="font-size: 1.25rem;">${msg}</p>
            `;
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('hidden');
                isProcessing = false;
                 if(msg.includes('Registrasi')) window.location.reload();
            }, 3000);
        }

        function speak(text) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }

        purposeSelect.addEventListener('change', (e) => {
             if (e.target.value === 'Wali Kelas') {
                classInfoGroup.classList.remove('hidden');
                classInfoInput.required = true;
            } else {
                classInfoGroup.classList.add('hidden');
                classInfoInput.required = false;
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'U') window.location.href = '/admin';
        });
    </script>
</body>

</html>
