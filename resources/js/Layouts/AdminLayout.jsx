import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';

export default function AdminLayout({ children, title }) {
    const { url } = usePage();

    const navLinkClass = (path) =>
        url.startsWith(path)
            ? "flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 font-bold border-l-4 border-red-600 transition-all"
            : "flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium transition-all border-l-4 border-transparent";

    return (
        <div className="flex min-h-screen bg-slate-50 font-sans">
            <Head title={title} />

            {/* Sidebar */}
            <aside className="w-64 bg-white border-r border-slate-200 fixed inset-y-0 z-30 flex flex-col shadow-sm print:hidden">
                <div className="h-16 flex items-center px-6 border-b border-slate-100">
                    <div className="w-8 h-8 bg-blue-700 text-white rounded flex items-center justify-center font-bold mr-3">DF</div>
                    <span className="font-bold text-slate-800 text-lg tracking-tight">Admin Panel</span>
                </div>

                <nav className="flex-1 py-6 space-y-1">
                    <Link href="/admin/monitoring" className={navLinkClass('/admin/monitoring')}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                            <path fillRule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clipRule="evenodd" />
                        </svg>
                        <span>Monitoring Spy</span>
                    </Link>
                    <Link href="/admin/vip" className={navLinkClass('/admin/vip')}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path fillRule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clipRule="evenodd" />
                        </svg>
                        <span>Tamu Terdaftar</span>
                    </Link>
                    <Link href="/admin/reports" className={navLinkClass('/admin/reports')}>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                        </svg>
                        <span>Laporan Data</span>
                    </Link>
                </nav>

                <div className="p-4 border-t border-slate-100 bg-slate-50">
                    <Link href="/" className="flex items-center justify-center gap-2 w-full py-2.5 bg-white border border-slate-300 text-slate-700 rounded font-semibold hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all text-sm group">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5 group-hover:-translate-x-1 transition-transform">
                            <path fillRule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clipRule="evenodd" />
                        </svg>
                        <span>Exit to Scanner</span>
                    </Link>
                </div>
            </aside>

            {/* Main Content */}
            <main className="flex-1 ml-64 bg-slate-50 min-h-screen print:ml-0 print:bg-white">
                <header className="h-16 bg-white border-b border-slate-200 sticky top-0 z-20 px-8 flex items-center justify-between shadow-sm print:hidden">
                    <h1 className="text-lg font-bold text-slate-800 border-l-4 border-blue-600 pl-3 leading-none py-1">
                        {title}
                    </h1>
                    <div className="flex items-center gap-4">
                        <div className="text-right">
                            <div className="text-sm font-bold text-slate-800">Administrator</div>
                            <div className="text-xs text-slate-500">Super User Access</div>
                        </div>
                        <div className="w-10 h-10 bg-slate-200 rounded-full border-2 border-white shadow-sm"></div>
                    </div>
                </header>

                <div className="p-8 print:p-0">
                    {children}
                </div>
            </main>
        </div>
    );
}
