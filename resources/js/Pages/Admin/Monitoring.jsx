import React, { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Monitoring({ activeVisits, stats }) {

    const [modalState, setModalState] = useState({ isOpen: false, visitId: null, time: '' });

    const openCheckoutModal = (id) => {
        setModalState({ isOpen: true, visitId: id, time: '' });
    };

    const closeCheckoutModal = () => {
        setModalState({ ...modalState, isOpen: false });
    };

    const submitCheckout = (e) => {
        e.preventDefault();
        router.post(`/admin/visits/${modalState.visitId}/checkout`, {
            check_out_time: modalState.time // Send the specific time
        }, {
            onSuccess: () => closeCheckoutModal(),
        });
    };

    return (
        <AdminLayout title="Monitoring Spy">
            <Head title="Monitoring" />

            {/* Modal Overlay */}
            {modalState.isOpen && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                    <div className="bg-white rounded-lg shadow-xl w-full max-w-sm border border-slate-200 overflow-hidden transform transition-all scale-100">
                        <div className="p-6">
                            <h3 className="text-lg font-bold text-slate-800 mb-2">Konfirmasi kepulangan tamu</h3>
                            <p className="text-sm text-slate-500 mb-4">Tentukan jam keluar tamu ini secara manual jika diperlukan.</p>

                            <form onSubmit={submitCheckout}>
                                <div className="mb-4">
                                    <label className="block text-xs font-bold uppercase text-slate-500 mb-1">Jam Keluar</label>
                                    <input
                                        type="time"
                                        value={modalState.time}
                                        onChange={(e) => setModalState({ ...modalState, time: e.target.value })}
                                        className="w-full px-3 py-2 border border-slate-300 rounded font-mono text-lg font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none"
                                        required
                                    />
                                </div>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={closeCheckoutModal}
                                        className="flex-1 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded hover:bg-slate-200 transition"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        className="flex-1 py-2 text-sm font-bold text-white bg-red-600 rounded hover:bg-red-700 transition"
                                    >
                                        Checkout Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Dashboard Stats */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {/* ... existing stats ... */}
                <div className="bg-white rounded p-6 border border-slate-200 border-l-4 border-l-blue-600 shadow-sm relative overflow-hidden">
                    <div className="flex justify-between items-start">
                        <div>
                            <div className="text-slate-500 text-sm font-bold uppercase tracking-wide mb-1">Tamu Aktif</div>
                            <div className="text-4xl font-bold text-slate-800">{activeVisits.length}</div>
                        </div>
                        <div className="w-10 h-10 bg-blue-50 rounded flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-6 h-6">
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                <path fillRule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clipRule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded p-6 border border-slate-200 border-l-4 border-l-green-500 shadow-sm relative overflow-hidden">
                    <div className="flex justify-between items-start">
                        <div>
                            <div className="text-slate-500 text-sm font-bold uppercase tracking-wide mb-1">Total Hari Ini</div>
                            <div className="text-4xl font-bold text-slate-800">{stats?.total_today || 0}</div>
                        </div>
                        <div className="w-10 h-10 bg-green-50 rounded flex items-center justify-center text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-6 h-6">
                                <path fillRule="evenodd" d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z" clipRule="evenodd" />
                                <path fillRule="evenodd" d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z" clipRule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div className="bg-white rounded p-6 border border-slate-200 border-l-4 border-l-slate-500 shadow-sm relative overflow-hidden">
                    <div className="flex justify-between items-start">
                        <div>
                            <div className="text-slate-500 text-sm font-bold uppercase tracking-wide mb-1">Total Tamu Terdaftar</div>
                            <div className="text-4xl font-bold text-slate-800">{stats?.total_guests || 0}</div>
                        </div>
                        <div className="w-10 h-10 bg-slate-50 rounded flex items-center justify-center text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-6 h-6">
                                <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div className="bg-white rounded shadow-sm border border-slate-200 overflow-hidden">
                <div className="p-6 border-b border-slate-200 flex justify-between items-center bg-white">
                    <div>
                        <h2 className="font-bold text-slate-800 text-lg">Daftar Kunjungan Aktif</h2>
                        <p className="text-slate-500 text-xs mt-1 uppercase tracking-wide">Pantau tamu di area sekolah</p>
                    </div>
                    <Link href="/admin/monitoring" className="px-4 py-2 text-sm font-bold bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={2} stroke="currentColor" className="w-4 h-4">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Refresh
                    </Link>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm">
                        <thead className="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th className="px-6 py-4">Jam Check-In</th>
                                <th className="px-6 py-4">Tamu</th>
                                <th className="px-6 py-4">Keperluan</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {activeVisits.length > 0 ? (
                                activeVisits.map((visit) => (
                                    <tr key={visit.id} className="hover:bg-slate-50 transition-colors">
                                        <td className="px-6 py-4 font-mono text-slate-600 font-bold">
                                            {new Date(visit.check_in_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-4">
                                                <img
                                                    src={`/storage/${visit.guest.photo_path}`}
                                                    alt={visit.guest.name}
                                                    className="w-10 h-10 rounded object-cover shadow-sm border border-slate-200"
                                                />
                                                <div>
                                                    <div className="font-bold text-slate-800">{visit.guest.name}</div>
                                                    <div className="text-xs text-slate-500 uppercase">
                                                        {visit.guest.institution || visit.guest.guest_type}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-slate-600">{visit.purpose}</td>
                                        <td className="px-6 py-4">
                                            <span className="inline-block px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                ACTIVE
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <button
                                                onClick={() => openCheckoutModal(visit.id)}
                                                className="px-4 py-2 text-xs font-bold text-white bg-red-600 rounded hover:bg-red-700 transition-all shadow-sm"
                                            >
                                                FORCE OUT
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-20 text-center text-slate-400">
                                        Tidak ada kunjungan aktif.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AdminLayout>
    );
}
