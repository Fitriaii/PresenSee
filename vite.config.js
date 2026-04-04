import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // HMR aktif
        }),
    ],
    // server: {
    //     host: "192.168.1.4",       // agar bisa diakses dari IP LAN (0.0.0.0)
    //     port: 5173,        // port untuk dev server Vite
    //     strictPort: true,  // jika port 5173 dipakai, akan error, bukan mencari alternatif
    // },
});
