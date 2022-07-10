import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import ElementPlus from 'unplugin-element-plus/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ElementPlus({
            // options
        }),
    ],
    resolve: {
        alias: {
            '@docs': '/resources/js/documentation'
        }
    }
});
