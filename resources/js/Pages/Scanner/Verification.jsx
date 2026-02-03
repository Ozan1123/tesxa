// ... imports same ...
import React, { useState, useEffect, useRef } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';
import { useFaceApi } from '@/Hooks/useFaceApi';

export default function Verification() {
    const { isModelLoaded, status, faceMatcher, videoRef, canvasRef, loadDescriptors, startVideo, faceapi } = useFaceApi();

    // UI State
    const [scannedGuest, setScannedGuest] = useState(null);
    const [scanStatus, setScanStatus] = useState('idle'); // idle, scanning, unknown, verified
    const [isProcessing, setIsProcessing] = useState(false);

    // Interval Ref
    const intervalRef = useRef(null);
    const unknownTimerRef = useRef(null);
    const processingRef = useRef(false);

    useEffect(() => {
        if (isModelLoaded) {
            loadDescriptors().then(startVideo);
        }
    }, [isModelLoaded]);

    const handleVideoPlay = () => {
        const video = videoRef.current;
        const canvas = canvasRef.current;
        if (!video || !canvas || !faceapi) return;

        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(canvas, displaySize);

        intervalRef.current = setInterval(async () => {
            if (video.paused || video.ended || processingRef.current || scannedGuest) return;

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            if (detections.length > 0) {
                setScanStatus('scanning');

                if (faceMatcher) {
                    const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

                    // We only care about the best result (closest face)
                    const bestResult = results[0];

                    if (bestResult) {
                        const box = resizedDetections[0].detection.box;
                        const isUnknown = bestResult.label === 'unknown';

                        const drawBox = new faceapi.draw.DrawBox(box, {
                            label: isUnknown ? 'Tidak Dikenal' : bestResult.label,
                            boxColor: isUnknown ? '#ef4444' : '#2563eb'
                        });
                        drawBox.draw(canvas);

                        if (!isUnknown) {
                            handleMatchFound(bestResult.label);
                        } else {
                            // Debounce unknown status
                            if (!unknownTimerRef.current) {
                                setScanStatus('unknown');
                                unknownTimerRef.current = setTimeout(() => {
                                    unknownTimerRef.current = null;
                                }, 1000);
                            }
                        }
                    }
                } else {
                    console.warn("FaceMatcher not loaded (No descriptors found or API error)");
                    setScanStatus('error');
                    // This will display the red "Gagal Memuat Data" bar we set up earlier
                }
            } else {
                setScanStatus('idle');
            }
        }, 500);
    };

    const handleMatchFound = async (guestId) => {
        if (processingRef.current) return;
        processingRef.current = true;
        setIsProcessing(true);
        setScanStatus('verified'); // Assume verified, but wait for data

        try {
            console.log("Checking status for Guest ID:", guestId);

            const res = await fetch('/api/visits/check-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ guest_id: guestId })
            });

            if (!res.ok) {
                // Handle 404/422/500
                const errorData = await res.json();
                throw new Error(errorData.message || `Server Error: ${res.status}`);
            }

            const data = await res.json();

            if (data.guest) {
                const guest = data.guest;

                // Auto Check-in logic
                await fetch('/api/visits/check-in', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ guest_id: guest.id })
                });

                setScannedGuest(guest);
                const msg = `Hello ${guest.name}, welcome.`;
                const utterance = new SpeechSynthesisUtterance(msg);
                utterance.lang = 'en-US';
                window.speechSynthesis.cancel(); // Cancel previous
                window.speechSynthesis.speak(utterance);
            } else {
                throw new Error("Data tamu tidak ditemukan.");
            }

        } catch (e) {
            console.error("Verification Error:", e);
            processingRef.current = false;
            setIsProcessing(false);
            setScanStatus('error');
            // Optional: You can add a state to show specific error text if needed
            // For now, 'error' status will show red bar
        }
    };

    const resetScanner = () => {
        processingRef.current = false;
        setScannedGuest(null);
        setIsProcessing(false);
        setScanStatus('idle');
    };

    const handleDeleteGuest = async (guestId) => {
        if (!confirm('Apakah Anda yakin ingin MENGHAPUS data tamu ini secara permanen?')) return;

        try {
            const res = await fetch(`/guests/${guestId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (res.ok) {
                alert('Data tamu berhasil dihapus.');
                // Reload descriptors to remove the deleted face from matcher
                setFaceMatcher(null); // Clear matcher temporarily
                await loadDescriptors();
                resetScanner();
            } else {
                alert('Gagal menghapus data.');
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menghapus data.');
        }
    };

    // Helper for Status UI
    const getStatusUI = () => {
        switch (scanStatus) {
            case 'verified':
                return { color: 'bg-blue-600', text: 'Verifikasi Berhasil', icon: 'check' };
            case 'unknown':
                return { color: 'bg-red-500', text: 'Wajah Tidak Terdaftar', icon: 'x' };
            case 'error':
                return { color: 'bg-red-700', text: 'Gagal Memuat Data', icon: 'alert' };
            case 'scanning':
                return { color: 'bg-yellow-500', text: 'Mencocokkan Wajah...', icon: 'search' };
            case 'idle':
            default:
                return { color: 'bg-slate-700', text: 'Posisikan Wajah di Kamera', icon: 'camera' };
        }
    };

    const ui = getStatusUI();

    return (
        <GuestLayout title="Verifikasi Tamu" fullScreen>
            <div className="flex flex-col h-screen bg-slate-50 font-sans">
                {/* Navbar */}
                <nav className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-40 shadow-sm relative">
                    <div className="flex items-center gap-4">
                        <div className="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md shadow-blue-100">DF</div>
                        <div>
                            <h1 className="font-bold text-slate-800 text-lg leading-tight">Devacto FaceID</h1>
                            <p className="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Verifikasi Tamu</p>
                        </div>
                    </div>
                    <Link href="/" className="px-5 py-2.5 text-xs font-bold uppercase tracking-wide text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-4 h-4">
                            <path fillRule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clipRule="evenodd" />
                        </svg>
                        Kembali
                    </Link>
                </nav>

                {/* Main */}
                <main className="flex-1 flex items-center justify-center p-8 relative">
                    <div className={`relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border-4 transition-colors duration-300 ${scanStatus === 'verified' ? 'border-blue-500' : scanStatus === 'unknown' ? 'border-red-500' : 'border-white'}`}>

                        {/* Status Bar */}
                        {!scannedGuest && (
                            <div className={`absolute top-0 left-0 w-full py-3 text-white font-bold text-center uppercase tracking-widest text-sm z-20 shadow-lg transition-colors duration-300 ${ui.color}`}>
                                {ui.text}
                            </div>
                        )}

                        {/* Video */}
                        <div className="relative aspect-[4/3] bg-slate-900 mt-0">
                            <video
                                ref={videoRef}
                                autoPlay
                                muted
                                playsInline
                                onPlay={handleVideoPlay}
                                className={`w-full h-full object-cover transform -scale-x-100 transition-opacity duration-500 ${isProcessing ? 'opacity-50 blur-sm' : 'opacity-100'}`}
                            />
                            <canvas ref={canvasRef} className="absolute inset-0 w-full h-full pointer-events-none" />

                            {/* Scanning Border Effect */}
                            {scanStatus === 'scanning' && !scannedGuest && (
                                <div className="absolute inset-0 border-[6px] border-yellow-400/50 animate-pulse pointer-events-none"></div>
                            )}

                            {/* Unknown Face Overlay */}
                            {scanStatus === 'unknown' && !scannedGuest && (
                                <div className="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-red-900/90 to-transparent flex flex-col items-center justify-end h-1/2 pointer-events-none animate-in slide-in-from-bottom-10">
                                    <div className="bg-red-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-2 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor" className="w-8 h-8">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </div>
                                    <h3 className="text-2xl font-bold text-white">Wajah Tidak Dikenal</h3>
                                    <p className="text-red-100 mb-4">Silakan daftar terlebih dahulu di menu utama.</p>
                                </div>
                            )}
                        </div>

                        {/* Success Overlay / ID Card */}
                        {scannedGuest && (
                            <div className="absolute inset-0 bg-white/95 z-30 flex flex-col items-center justify-center text-center p-8 animate-in fade-in zoom-in duration-300">
                                <div className="bg-white border border-slate-200 rounded-2xl p-8 shadow-2xl w-full max-w-sm relative overflow-hidden">
                                    <div className="absolute top-0 left-0 w-full h-2 bg-blue-600"></div>
                                    <img
                                        src={`/storage/${scannedGuest.photo_path}`}
                                        alt={scannedGuest.name}
                                        className="w-32 h-32 rounded-full object-cover border-4 border-blue-100 shadow-md mx-auto mb-6"
                                    />
                                    <h2 className="text-2xl font-bold text-slate-900 mb-1">{scannedGuest.name}</h2>
                                    <p className="text-sm font-bold text-blue-600 uppercase tracking-wide mb-6 bg-blue-50 py-1 px-3 rounded-full inline-block">{scannedGuest.purpose || scannedGuest.guest_type}</p>

                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between text-sm border-b border-slate-100 pb-2">
                                            <span className="text-slate-500">Waktu Masuk</span>
                                            <span className="font-mono font-bold text-slate-700">{new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                        </div>
                                        <div className="flex items-center justify-between text-sm border-b border-slate-100 pb-2">
                                            <span className="text-slate-500">Status</span>
                                            <span className="font-bold text-green-600 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-4 h-4">
                                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                                </svg>
                                                Terverifikasi
                                            </span>
                                        </div>
                                    </div>

                                    <div className="flex gap-3 mt-8">
                                        <button
                                            onClick={resetScanner}
                                            className="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg transition-all transform hover:scale-[1.02] active:scale-95 uppercase tracking-wide text-sm"
                                        >
                                            Selesai
                                        </button>
                                        <button
                                            onClick={() => handleDeleteGuest(scannedGuest.id)}
                                            className="px-4 py-3 border-2 border-red-100 text-red-500 rounded-xl font-bold hover:bg-red-50 hover:border-red-200 transition-all active:scale-95 text-sm"
                                            title="Hapus Data Tamu Ini"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor" className="w-5 h-5">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </main>
            </div>
        </GuestLayout>
    );
}
