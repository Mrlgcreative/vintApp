import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // Sur Hostinger, public/ est à la racine
        outDir: process.env.VITE_BUILD_PATH || 'public/build',
        emptyOutDir: true,
    },
});
