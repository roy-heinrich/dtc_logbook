import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                ui: 'resources/css/app.css',
                frontend: 'resources/js/app.js',
                adminDashboard: 'resources/js/admin-dashboard.js',
            },
            refresh: true,
        }),
    ],
});
