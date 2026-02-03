import React from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

export default function Reports({ visits }) {

    // Pagination Component
    const Pagination = ({ links }) => (
        <div className="flex flex-wrap justify-center gap-1 mt-6">
            {links.map((link, key) => (
                link.url === null ? (
                    <div key={key} className="px-3 py-1 text-sm text-slate-400 border border-slate-200 rounded opacity-50 cursor-not-allowed" dangerouslySetInnerHTML={{ __html: link.label }} />
                ) : (
                    <Link
                        key={key}
                        href={link.url}
                        className={`px-3 py-1 text-sm border rounded ${link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'}`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                )
            ))}
        </div>
    );

    return (
        <AdminLayout title="Laporan Kunjungan">

            {/* Print Header (Visible ONLY on Print) */}
            <div className="hidden print:block mb-8 text-center border-b-2 border-black pb-4">
                <h1 className="text-2xl font-bold uppercase tracking-wider mb-1">Laporan Kunjungan Tamu</h1>
                <p className="text-sm text-slate-600">Dicetak pada: {new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            </div>

            {/* Header Stats & Tools (Hidden on Print) */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 print:hidden">
                <div>
                    <h2 className="text-xl font-bold text-slate-800 border-l-4 border-blue-600 pl-3">Laporan Kunjungan</h2>
                    <p className="text-slate-500 text-sm pl-4">Arsip data kunjungan tamu.</p>
                </div>
                <div className="flex gap-3">
                    <button onClick={() => window.print()} className="px-5 py-2 text-sm bg-white border border-slate-300 text-slate-700 font-bold rounded hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path fillRule="evenodd" d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 003 3h.27l-.155 1.705A1.875 1.875 0 007.232 22.5h9.536a1.875 1.875 0 001.867-2.045l-.155-1.705h.27a3 3 0 003-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.816 48.816 0 0018 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM16.5 6.205v-2.83A.375.375 0 0016.125 3h-8.25a.375.375 0 00-.375.375v2.83a49.353 49.353 0 019 0z" clipRule="evenodd" />
                            <path d="M15.058 13.91l-.228 2.503a.375.375 0 01-.373.34h-4.914a.375.375 0 01-.373-.34l-.228-2.503a.75.75 0 01.75-.818h4.616a.75.75 0 01.75.818z" />
                        </svg>
                        <span>Cetak PDF</span>
                    </button>
                    <button className="px-5 py-2 text-sm bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path fillRule="evenodd" d="M12 2.25a.75.75 0 01.75.75v11.69l3.22-3.22a.75.75 0 111.06 1.06l-4.5 4.5a.75.75 0 01-1.06 0l-4.5-4.5a.75.75 0 111.06-1.06l3.22 3.22V3a.75.75 0 01.75-.75zm-9 13.5a.75.75 0 01.75.75v2.25a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5V16.5a.75.75 0 011.5 0v2.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V16.5a.75.75 0 01.75-.75z" clipRule="evenodd" />
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>
            </div>

            <div className="bg-white rounded shadow-sm border border-slate-200 overflow-hidden print:shadow-none print:border-none">
                <div className="overflow-x-auto">
                    <table className="w-full text-left text-sm print:text-xs">
                        <thead className="bg-slate-50 text-slate-600 uppercase text-xs tracking-wider font-bold border-b border-slate-200 print:bg-white print:border-black">
                            <tr>
                                <th className="px-6 py-4 print:px-2 print:py-2">Tanggal</th>
                                <th className="px-6 py-4 print:px-2 print:py-2">Tamu</th>
                                <th className="px-6 py-4 print:px-2 print:py-2">Instansi</th>
                                <th className="px-6 py-4 print:px-2 print:py-2">Tujuan</th>
                                <th className="px-6 py-4 font-mono text-center print:px-2 print:py-2">Masuk</th>
                                <th className="px-6 py-4 font-mono text-center print:px-2 print:py-2">Keluar</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100 print:divide-slate-200">
                            {visits.data.length > 0 ? (
                                visits.data.map((visit) => (
                                    <tr key={visit.id} className="hover:bg-slate-50 transition-colors print:hover:bg-transparent">
                                        <td className="px-6 py-4 print:px-2 print:py-2">
                                            <div className="font-bold text-slate-700">
                                                {new Date(visit.check_in_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 print:px-2 print:py-2">
                                            <div className="font-bold text-slate-800">{visit.guest.name}</div>
                                            <div className="text-xs text-slate-500">ID: #{visit.id}</div>
                                        </td>
                                        <td className="px-6 py-4 print:px-2 print:py-2">
                                            <span className="text-slate-600">
                                                {visit.guest.institution || visit.guest.guest_type}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-slate-600 print:px-2 print:py-2">{visit.purpose}</td>
                                        <td className="px-6 py-4 text-center font-mono font-bold text-green-600 print:px-2 print:py-2 print:text-black">
                                            {new Date(visit.check_in_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        </td>
                                        <td className="px-6 py-4 text-center font-mono font-bold text-red-600 print:px-2 print:py-2 print:text-black">
                                            {visit.check_out_at ? (
                                                new Date(visit.check_out_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                                            ) : (
                                                <span className="text-slate-400 text-xs italic">Aktif</span>
                                            )}
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-slate-400">
                                        Belum ada riwayat kunjungan.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="p-6 border-t border-slate-100 bg-slate-50 print:hidden">
                    <Pagination links={visits.links} />
                </div>
            </div>

            {/* Print Signature Section (Visible ONLY on Print) */}
            <div className="hidden print:flex justify-end mt-12 px-8">
                <div className="text-center">
                    <p className="mb-20 text-sm">Mengetahui,<br />Guru Piket</p>
                    <div className="border-t border-black w-48 mx-auto"></div>
                    <p className="mt-2 text-sm font-bold">( ............................................ )</p>
                </div>
            </div>
        </AdminLayout>
    );
}
