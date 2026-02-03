import React, { useState, useEffect, useRef } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import { useFaceApi } from '@/Hooks/useFaceApi';

export default function Index() {
    const { isModelLoaded, status, faceMatcher, videoRef, canvasRef, loadDescriptors, startVideo, faceapi } = useFaceApi();

    // UI State
    const [started, setStarted] = useState(false);
    const [detectedFace, setDetectedFace] = useState(null); // { gender, descriptor }
    const [isProcessing, setIsProcessing] = useState(false);

    // Modals
    const [checkInGuest, setCheckInGuest] = useState(null);
    const [checkOutGuest, setCheckOutGuest] = useState(null);
    const [showSuccess, setShowSuccess] = useState(null);

    // Form
    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
        purpose: 'Melihat Pameran',
        guest_type: 'Tamu Umum',
        class_info: '',
        gender: 'male',
        image: '',
        face_descriptor: ''
    });

    // Refs for intervals
    const intervalRef = useRef(null);

    // Initial Load
    useEffect(() => {
        if (started && isModelLoaded) {
            loadDescriptors().then(startVideo);
        }
    }, [started, isModelLoaded]);

    // Detection Loop
    const handleVideoPlay = () => {
        const video = videoRef.current;
        const canvas = canvasRef.current;
        if (!video || !canvas || !faceapi) return;

        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);

        intervalRef.current = setInterval(async () => {
            if (video.paused || video.ended || isProcessing || checkInGuest || checkOutGuest) return;

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors()
                .withAgeAndGender();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (detections.length > 0) {
                const detection = resizedDetections[0];
                setDetectedFace({ gender: detection.gender, descriptor: detection.descriptor });

                // Draw
                const box = detection.detection.box;
                ctx.strokeStyle = '#3b82f6';
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);

                // Identify
                if (faceMatcher) {
                    const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                    if (bestMatch.label !== 'unknown') {
                        handleKnownGuest(bestMatch.label);
                    }
                }
            } else {
                setDetectedFace(null);
            }
        }, 1000);
    };

    const handleKnownGuest = async (guestId) => {
        setIsProcessing(true);
        try {
            const res = await fetch('/api/visits/check-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: guestId })
            });
            const response = await res.json();

            if (response.status === 'active') {
                setCheckOutGuest({ ...response.guest, visit: response.visit });
            } else {
                setCheckInGuest(response.guest);
            }
        } catch (e) {
            console.error(e);
            setIsProcessing(false);
        }
    };

    const confirmCheckIn = async (purpose) => {
        if (!processAction()) return;
        try {
            await fetch('/api/visits/check-in', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: checkInGuest.id, purpose })
            });
            setCheckInGuest(null);
            flashSuccess(`Selamat Datang, ${checkInGuest.name}`);
        } catch (e) { console.error(e); setIsProcessing(false); }
    };

    const confirmCheckOut = async () => {
        if (!processAction()) return;
        try {
            await fetch('/api/visits/check-out', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: checkOutGuest.id })
            });
            setCheckOutGuest(null);
            flashSuccess(`Sampai Jumpa, ${checkOutGuest.name}`);
        } catch (e) { console.error(e); setIsProcessing(false); }
    };

    const processAction = () => {
        /* Optional: Add rate limiting or UI feedback */
        return true;
    };

    const flashSuccess = (msg) => {
        setShowSuccess(msg);
        speak(msg);
        setTimeout(() => {
            setShowSuccess(null);
            setIsProcessing(false); // Resume loop
        }, 3000);
    };

    const speak = (text) => {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        window.speechSynthesis.speak(utterance);
    };

    // Registration Form
    const submitRegistration = (e) => {
        e.preventDefault();

        // Capture Image
        if (videoRef.current) {
            const capCanvas = document.createElement('canvas');
            capCanvas.width = videoRef.current.videoWidth;
            capCanvas.height = videoRef.current.videoHeight;
            capCanvas.getContext('2d').drawImage(videoRef.current, 0, 0);
            data.image = capCanvas.toDataURL('image/png');
        }

        data.gender = detectedFace?.gender || 'male';
        if (detectedFace?.descriptor) {
            data.face_descriptor = JSON.stringify(Array.from(detectedFace.descriptor));
        }

        post('/guests/store', {
            onSuccess: () => {
                flashSuccess('Registrasi Berhasil!');
                reset();
                loadDescriptors(); // Refresh matcher
            }
        });
    };

    return (
        <GuestLayout title="Scanner" fullScreen>
            {/* Overlay Start */}
            {!started && (
                <div className="absolute inset-0 z-50 overflow-hidden bg-zinc-100 flex items-center justify-center font-sans selection:bg-blue-100">

                    {/* PS2-inspired Monochromatic Background */}
                    <div className="absolute inset-0 overflow-hidden pointer-events-none">
                        <style>{`
                            @keyframes float {
                                0% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
                                50% { transform: translateY(-20px) rotate(2deg); opacity: 0.6; }
                                100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
                            }
                            @keyframes drift {
                                0% { transform: translate(0, 0); }
                                50% { transform: translate(10px, -15px); }
                                100% { transform: translate(0, 0); }
                            }
                            .ps2-block {
                                position: absolute;
                                background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
                                border-radius: 12px;
                                opacity: 0.4;
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
                                animation: float 8s ease-in-out infinite;
                            }
                        `}</style>

                        {/* Random Floating Blocks */}
                        {/* Top Left Cluster */}
                        <div className="ps2-block w-32 h-40 top-[10%] left-[5%] delay-0" style={{ animationDelay: '0s' }}></div>
                        <div className="ps2-block w-24 h-24 top-[25%] left-[15%] delay-75" style={{ animationDelay: '2s' }}></div>

                        {/* Top Right Cluster */}
                        <div className="ps2-block w-40 h-32 top-[15%] right-[10%] delay-100" style={{ animationDelay: '1s' }}></div>
                        <div className="ps2-block w-20 h-20 top-[30%] right-[20%] delay-150" style={{ animationDelay: '3s' }}></div>

                        {/* Bottom Left Cluster */}
                        <div className="ps2-block w-36 h-36 bottom-[20%] left-[8%] delay-200" style={{ animationDelay: '4s' }}></div>
                        <div className="ps2-block w-28 h-48 bottom-[10%] left-[20%] delay-300" style={{ animationDelay: '1.5s' }}></div>

                        {/* Bottom Right Cluster */}
                        <div className="ps2-block w-44 h-32 bottom-[25%] right-[12%] delay-500" style={{ animationDelay: '2.5s' }}></div>
                        <div className="ps2-block w-24 h-24 bottom-[15%] right-[25%] delay-700" style={{ animationDelay: '5s' }}></div>

                        {/* Distant/Small Particles */}
                        <div className="ps2-block w-12 h-12 top-[50%] left-[10%] opacity-20" style={{ animationDuration: '12s' }}></div>
                        <div className="ps2-block w-16 h-16 top-[60%] right-[8%] opacity-20" style={{ animationDuration: '15s' }}></div>
                    </div>

                    <div className="relative z-20 flex flex-col items-center animate-in fade-in zoom-in duration-500 bg-white/60 backdrop-blur-sm p-12 rounded-[3rem] border border-white/50 shadow-2xl ring-1 ring-white/50">
                        <div className="w-24 h-24 bg-blue-600 text-white text-3xl font-bold flex items-center justify-center rounded-3xl mb-8 shadow-xl shadow-blue-200 ring-4 ring-white">
                            DF
                        </div>
                        <h1 className="text-4xl font-extrabold text-slate-800 mb-2 tracking-tight drop-shadow-sm">Devacto FaceID</h1>
                        <p className="text-slate-500 mb-10 text-lg font-medium tracking-wide">Sistem Verifikasi & Registrasi Tamu</p>

                        <div className="flex flex-col sm:flex-row gap-5 w-full max-w-md">
                            <button
                                onClick={() => setStarted(true)}
                                className="flex-1 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-200/50 transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-wide"
                            >
                                Mulai Sistem
                            </button>
                            <Link
                                href="/registered-guests"
                                className="flex-1 px-8 py-4 bg-white hover:bg-white/80 text-slate-700 border border-slate-200 rounded-2xl font-bold transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 group uppercase tracking-wide shadow-sm"
                            >
                                <span>Tamu Terdaftar</span>
                            </Link>
                        </div>

                        <div className="mt-10 flex items-center gap-2 text-[10px] font-mono text-slate-400 uppercase tracking-widest bg-slate-100/50 px-4 py-1 rounded-full border border-slate-200/50">
                            <span className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            System Ready
                        </div>
                    </div>
                </div>
            )}

            {/* Success Overlay */}
            {showSuccess && (
                <div className="absolute inset-0 bg-white/95 z-50 flex flex-col items-center justify-center animate-in fade-in zoom-in duration-300">
                    <div className="w-24 h-24 bg-green-500 text-white flex items-center justify-center rounded-full mb-6 shadow-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-14 h-14">
                            <path fillRule="evenodd" d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z" clipRule="evenodd" />
                        </svg>
                    </div>
                    <h2 className="text-3xl font-bold text-slate-900 mb-2">Berhasil</h2>
                    <p className="text-xl text-slate-600">{showSuccess}</p>
                </div>
            )}

            {/* Navbar */}
            <div className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-40 relative shadow-sm">
                <div className="flex items-center gap-4">
                    <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md shadow-blue-100">DF</div>
                    <div>
                        <h1 className="font-bold text-slate-800 text-lg leading-tight">Devacto FaceID</h1>
                        <p className="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Secure Gate System</p>
                    </div>
                </div>
                <a href="/admin/monitoring" className="px-5 py-2.5 text-xs font-bold uppercase tracking-wide text-blue-700 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-all">
                    Dashboard Admin
                </a>
            </div>

            {/* Main Content */}
            <div className="flex-1 p-8 flex items-center justify-center bg-slate-50 relative">

                <div className="bg-white rounded-3xl shadow-xl flex w-full max-w-6xl h-[650px] overflow-hidden border border-slate-200 relative z-10">

                    {/* Video Area */}
                    <div className="relative flex-1 bg-slate-100 group border-r border-slate-200">
                        <video
                            ref={videoRef}
                            autoPlay
                            muted
                            playsInline
                            onPlay={handleVideoPlay}
                            className="w-full h-full object-cover transform -scale-x-100"
                        />
                        <canvas ref={canvasRef} className="absolute inset-0 w-full h-full pointer-events-none" />

                        {/* Camera UI Overlay */}
                        <div className="absolute top-6 left-6 flex gap-2">
                            <div className="px-4 py-2 bg-white/90 backdrop-blur text-xs font-bold text-slate-700 rounded-lg shadow-sm flex items-center gap-2">
                                <span className="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                LIVE CAMERA
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Form */}
                    <div className="w-96 p-8 flex flex-col justify-center bg-white border-l border-slate-100 shadow-xl relative z-20">
                        <div className="mb-8 relative">
                            <h2 className="text-xl font-bold text-slate-800 mb-1">Identifikasi Tamu</h2>
                            <div className="w-12 h-1 bg-blue-600 rounded-full"></div>
                        </div>

                        <div className={`mb-8 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-3 border transition-all duration-300 ${detectedFace ? 'bg-blue-50 border-blue-200 text-blue-700 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-400'}`}>
                            <div className={`w-3 h-3 rounded-full shadow-sm ${detectedFace ? 'bg-blue-600 animate-pulse ring-2 ring-blue-200' : 'bg-slate-300'}`}></div>
                            {detectedFace ? "Wajah Terdeteksi" : "Menunggu scan wajah..."}
                        </div>

                        <form onSubmit={submitRegistration} className="space-y-6">
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Nama Lengkap</label>
                                <div className="relative group">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                                            <path fillRule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        className="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white placeholder-slate-400 transition-all font-bold outline-none"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        required
                                        placeholder="Nama tamu..."
                                    />
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Sebagai</label>
                                <div className="relative group">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                                            <path fillRule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <select
                                        className="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition-all font-bold appearance-none outline-none cursor-pointer"
                                        value={data.guest_type}
                                        onChange={e => setData('guest_type', e.target.value)}
                                    >
                                        <option>Dinas</option>
                                        <option>Orang Tua</option>
                                        <option>Tamu Umum</option>
                                        <option>Alumni</option>
                                    </select>
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4">
                                            <path fillRule="evenodd" d="M12.53 16.28a.75.75 0 01-1.06 0l-7.5-7.5a.75.75 0 011.06-1.06L12 14.69l6.97-6.97a.75.75 0 111.06 1.06l-7.5 7.5z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Tujuan Kunjungan</label>
                                <div className="relative group">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                                            <path fillRule="evenodd" d="M11.54 22.351l.07.04.028.016a.75.75 0 00.724 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                    <select
                                        className="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition-all font-bold appearance-none outline-none cursor-pointer"
                                        value={data.purpose}
                                        onChange={e => setData('purpose', e.target.value)}
                                    >
                                        <option>Melihat Pameran</option>
                                        <option>Walikelas</option>
                                        <option>Tata Usaha</option>
                                        <option>Konsultan</option>
                                        <option>Kedinasan</option>
                                    </select>
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4">
                                            <path fillRule="evenodd" d="M12.53 16.28a.75.75 0 01-1.06 0l-7.5-7.5a.75.75 0 011.06-1.06L12 14.69l6.97-6.97a.75.75 0 111.06 1.06l-7.5 7.5z" clipRule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                disabled={processing || !detectedFace}
                                className="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-[1.02] active:scale-95 mt-6 uppercase tracking-wide border border-blue-500/20"
                            >
                                {processing ? 'Menyimpan...' : 'Simpan Kunjungan'}
                            </button>

                            {!detectedFace && (
                                <p className="text-[10px] text-center text-slate-400 uppercase tracking-wide mt-2">Posisikan wajah di depan kamera</p>
                            )}
                        </form>
                    </div>
                </div>
            </div>

            {/* Check-In Modal */}
            {checkInGuest && (
                <div className="absolute inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl p-8 w-96 text-center shadow-2xl animate-in zoom-in duration-200">
                        <div className="text-4xl mb-4">👋</div>
                        <h2 className="text-2xl font-bold text-slate-900 mb-1">Selamat Datang</h2>
                        <h3 className="text-lg font-medium text-indigo-600 mb-6">{checkInGuest.name}</h3>

                        <p className="text-sm text-slate-500 mb-4">Konfirmasi tujuan kunjungan Anda:</p>
                        <input
                            type="text"
                            id="modal-purpose"
                            className="w-full px-3 py-2 border border-slate-300 rounded-lg mb-6"
                            placeholder="Contoh: Bertemu Kepala Sekolah"
                            defaultValue={checkInGuest.purpose || ''}
                        />

                        <div className="flex gap-3">
                            <button onClick={() => { setCheckInGuest(null); setIsProcessing(false); }} className="flex-1 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200">Batal</button>
                            <button onClick={() => confirmCheckIn(document.getElementById('modal-purpose').value)} className="flex-1 px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">Check In</button>
                        </div>
                    </div>
                </div>
            )}

            {/* Check-Out Modal */}
            {checkOutGuest && (
                <div className="absolute inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl p-8 w-96 text-center shadow-2xl animate-in zoom-in duration-200">
                        <div className="text-4xl mb-4">👋</div>
                        <h2 className="text-2xl font-bold text-slate-900 mb-1">Konfirmasi Pulang</h2>
                        <h3 className="text-lg font-medium text-indigo-600 mb-6">{checkOutGuest.name}</h3>

                        <p className="text-sm text-slate-500 mb-6">Apakah Anda ingin menyelesaikan kunjungan ini?</p>

                        <div className="flex gap-3">
                            <button onClick={() => { setCheckOutGuest(null); setIsProcessing(false); }} className="flex-1 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 font-medium hover:bg-slate-200">Batal</button>
                            <button onClick={confirmCheckOut} className="flex-1 px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700">Ya, Checkout</button>
                        </div>
                    </div>
                </div>
            )}
        </GuestLayout>
    );
}
