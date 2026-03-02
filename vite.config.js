import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                ui: 'resources/css/app.css',
                frontend: 'resources/js/app.js',
            },
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/chunk-[name].js',
                assetFileNames: (assetInfo) => {
                    const assetName = assetInfo.name ?? '';
                    if (assetName.endsWith('.css')) {
                        return 'assets/[name].css';
                    }
                    return 'assets/[name].[ext]';
                },
            },
        },
    },
});
