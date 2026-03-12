import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js',
'resources/css/app.css',
'resources/css/dashboard.css',
'resources/css/bpjs.css',
'resources/css/master_pasien_create.css',
'resources/css/master_pasien.css',
'resources/js/dashboard.js',
'resources/js/registrasi.js',
'resources/js/rawat_jalan.js'
            ],
            refresh: true,
        }),
    ],
});
