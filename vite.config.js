import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js', 
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        //   host: 'headoffice.lms',
        // port: 5173,
        // strictPort: true,
        // allowedHosts: ['headoffice.lms'],

        host: true, // allow access from LAN or custom domains
        port: 5173,
        strictPort: true,
        origin: 'http://localhost:5173',
        cors: true,
        allowedHosts: ['.lms'], // allow any *.lms
        
    },
});
