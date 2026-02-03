import React from 'react';
import { Head } from '@inertiajs/react';

export default function GuestLayout({ children, title, fullScreen = false }) {
    return (
        <div className={`min-h-screen bg-slate-50 flex flex-col ${fullScreen ? 'overflow-hidden h-screen' : ''}`}>
            <Head title={title} />
            {children}
        </div>
    );
}
