import { useState, useEffect, useRef } from 'react';
// Assuming face-api.min.js is in public/js/face-api.min.js.
// We can use it via window.faceapi or import it if installed via npm.
// For this migration, we'll assume it's loaded globally via script tag in app.blade.php
// OR we dynamically load the script.
// BETTER: Dynamic load to keep React pure.

export const useFaceApi = () => {
    const [isModelLoaded, setIsModelLoaded] = useState(false);
    const [status, setStatus] = useState("Initializing...");
    const [faceMatcher, setFaceMatcher] = useState(null);
    const videoRef = useRef(null);
    const canvasRef = useRef(null);

    // Load Script and Models
    useEffect(() => {
        const loadScript = () => {
            if (window.faceapi) return Promise.resolve();
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = '/js/face-api.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.body.appendChild(script);
            });
        };

        const loadModels = async () => {
            setStatus("Loading AI Models...");
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                faceapi.nets.ageGenderNet.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models')
            ]);
            setStatus("Models Loaded");
            setIsModelLoaded(true);
        };

        loadScript().then(loadModels).catch(e => setStatus("Error: " + e.message));
    }, []);

    // Load Descriptors
    const loadDescriptors = async () => {
        setStatus("Fetching Face Data...");
        try {
            const res = await fetch('/api/guests/descriptors');
            const guests = await res.json();

            if (guests.length > 0) {
                const labeledDescriptors = guests.map(guest => {
                    if(guest.face_descriptor) {
                        try {
                            const descriptor = new Float32Array(Object.values(JSON.parse(guest.face_descriptor)));
                            return new faceapi.LabeledFaceDescriptors(guest.id.toString(), [descriptor]);
                        } catch(e) { return null; }
                    }
                    return null;
                }).filter(d => d !== null);

                if(labeledDescriptors.length > 0) {
                    // Threshold 0.55 allows slight appearance changes (glasses on/off)
                    // while still being secure enough for identification
                    setFaceMatcher(new faceapi.FaceMatcher(labeledDescriptors, 0.55));
                }
            }
            setStatus("Ready");
        } catch (e) {
            console.error(e);
            setStatus("Error fetching data");
        }
    };

    // Start Video
    const startVideo = async () => {
        if (!videoRef.current) return;
        setStatus("Starting Camera...");
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'user'
                }
            });
            videoRef.current.srcObject = stream;
        } catch (err) {
            setStatus("Camera Error: " + err.message);
        }
    };

    return {
        isModelLoaded,
        status,
        faceMatcher,
        videoRef,
        canvasRef,
        loadDescriptors,
        startVideo,
        faceapi: window.faceapi // Expose for specific calls
    };
};
