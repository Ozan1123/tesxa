import React, { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, useForm, router } from '@inertiajs/react';
import { useFaceApi } from '@/Hooks/useFaceApi';

export default function VIP({ vips }) {
    const { isModelLoaded, faceapi } = useFaceApi();
    const [isAnalyzing, setIsAnalyzing] = useState(false);
    const [faceStatus, setFaceStatus] = useState(null); // 'valid', 'invalid', 'error'

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        institution: '',
        photo: null,
        face_descriptor: null,
    });

    const submit = (e) => {
        e.preventDefault();
        if (!data.face_descriptor) {
            alert("Wajah tidak terdeteksi! Pastikan foto jelas dan memiliki wajah.");
            return;
        }

        post('/admin/vip/store', {
            onSuccess: () => {
                reset();
                setFaceStatus(null);
            },
        });
    };

    const handlePhotoChange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setData('photo', file);

        if (!isModelLoaded || !faceapi) {
            alert("AI Model sedang memuat, silakan tunggu sebentar.");
            return;
        }

        setIsAnalyzing(true);
        setFaceStatus(null);

        try {
            // Create an image element to detect faces
            const img = await faceapi.bufferToImage(file);
            const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

            if (detection) {
                console.log("Face Descriptor Generated");
                setData(prev => ({
                    ...prev,
                    photo: file,
                    face_descriptor: JSON.stringify(detection.descriptor)
                }));
                setFaceStatus('valid');
            } else {
                console.warn("No face detected");
                setFaceStatus('invalid');
                setData('face_descriptor', null);
            }
        } catch (err) {
            console.error(err);
            setFaceStatus('error');
        } finally {
            setIsAnalyzing(false);
        }
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus data tamu ini?')) {
            router.delete(`/guests/${id}`);
        }
    };

    return (
        <AdminLayout title="Kelola Tamu Terdaftar">
            <Head title="Tamu Terdaftar" />

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Form Section */}
                <div className="lg:col-span-1">
                    <div className="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                        <div className="p-6">
                            <h2 className="text-lg font-bold text-slate-800 mb-1 border-l-4 border-blue-600 pl-3">Registrasi Tamu Baru</h2>
                            <p className="text-slate-500 text-xs mb-6 pl-4">
                                {isModelLoaded ? (
                                    <span className="text-green-600 font-bold">✓ AI Ready</span>
                                ) : (
                                    <span className="text-orange-500 animate-pulse">Loading AI Models...</span>
                                )}
                            </p>

                            <form onSubmit={submit} className="space-y-4">
                                <div>
                                    <label className="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        className="w-full px-3 py-2 bg-white border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        placeholder="Nama Tamu..."
                                    />
                                    {errors.name && <div className="text-red-500 text-xs mt-1">{errors.name}</div>}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1">Instansi / Jabatan</label>
                                    <input
                                        type="text"
                                        className="w-full px-3 py-2 bg-white border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                                        value={data.institution}
                                        onChange={e => setData('institution', e.target.value)}
                                        placeholder="Contoh: Dinas Pendidikan"
                                    />
                                    {errors.institution && <div className="text-red-500 text-xs mt-1">{errors.institution}</div>}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1">Foto Wajah</label>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-300 rounded"
                                        onChange={handlePhotoChange}
                                        disabled={!isModelLoaded || isAnalyzing}
                                    />
                                    <div className="mt-2 text-xs">
                                        {isAnalyzing && <span className="text-blue-500 font-bold animate-pulse">Menganalisis Wajah...</span>}
                                        {faceStatus === 'valid' && <span className="text-green-600 font-bold">✓ Wajah Valid & Terdeteksi</span>}
                                        {faceStatus === 'invalid' && <span className="text-red-500 font-bold">⚠ Wajah Tidak Dikenali. Gunakan foto lain.</span>}
                                        {faceStatus === 'error' && <span className="text-red-500 font-bold">Error memproses gambar.</span>}
                                    </div>
                                    <p className="text-[10px] text-slate-400 mt-1">*Format JPG/PNG. Wajah harus terlihat jelas untuk discan.</p>
                                    {errors.photo && <div className="text-red-500 text-xs mt-1">{errors.photo}</div>}
                                </div>

                                <button
                                    type="submit"
                                    disabled={processing || !data.face_descriptor || isAnalyzing}
                                    className="w-full py-3 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 disabled:opacity-50 transition uppercase tracking-wide text-xs"
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan Data'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {/* List Grid Section */}
                <div className="lg:col-span-2">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-lg font-bold text-slate-800">Daftar Tamu Terdaftar</h2>
                        <span className="bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-bold border border-slate-200">{vips.length} Data</span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {vips.length > 0 ? (
                            vips.map(vip => {
                                const lastVisit = vip.visits && vip.visits[0];
                                const isActive = lastVisit && !lastVisit.check_out_at;

                                return (
                                    <div key={vip.id} className={`bg-white rounded p-4 shadow-sm border ${isActive ? 'border-green-400 bg-green-50/30' : 'border-slate-200'} flex items-start gap-3 hover:border-blue-300 transition-colors relative`}>
                                        <img
                                            src={`/storage/${vip.photo_path}`}
                                            className="w-12 h-12 rounded object-cover border border-slate-100"
                                            alt={vip.name}
                                        />

                                        <div className="flex-1 min-w-0">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <h3 className="font-bold text-slate-800 truncate text-sm">{vip.name}</h3>
                                                    <p className="text-xs text-slate-500 truncate mb-1">{vip.institution}</p>

                                                    {/* Status Indicator */}
                                                    <div className="mt-1">
                                                        {isActive ? (
                                                            <span className="inline-flex items-center gap-1 text-[10px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full border border-green-200 animate-pulse">
                                                                <span className="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                                SEDANG BERKUNJUNG
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                                                <span className="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                                                {lastVisit ? `TERAKHIR: ${new Date(lastVisit.created_at).toLocaleDateString()}` : 'BELUM PERNAH BERKUNJUNG'}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <button
                                                    onClick={() => handleDelete(vip.id)}
                                                    className="text-slate-400 hover:text-red-500 transition-colors p-1"
                                                    title="Hapus Data"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-4 h-4">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div className="flex items-center gap-2 mt-2 pt-2 border-t border-slate-50">
                                                <span className="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 rounded border border-blue-100 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-3 h-3">
                                                        <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clipRule="evenodd" />
                                                    </svg>
                                                    VERIFIED ID
                                                </span>
                                                <span className="text-[10px] text-slate-400">Dibuat: {new Date(vip.created_at).toLocaleDateString()}</span>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        ) : (
                            <div className="col-span-full py-12 text-center bg-white rounded border border-dashed border-slate-300 flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-12 h-12 text-slate-300 mb-2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.41l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                                <p className="text-slate-500 text-sm">Belum ada data tamu.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
