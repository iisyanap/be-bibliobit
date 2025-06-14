import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/sb-admin-2.scss', // Hanya untuk CSS
                'resources/js/app.js',           // Entry point JS utama
                'resources/js/sb-admin-2.js', ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
