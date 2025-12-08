import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/style.css',
                'resources/js/navbar.js',
                'resources/js/sidebar.js',
                'resources/js/cursor-clouds.js',
                'resources/js/back-to-top.js',
                'resources/js/images.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    base: '/build/'
});
