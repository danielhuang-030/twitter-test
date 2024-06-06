import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import commonjs from '@rollup/plugin-commonjs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        commonjs()
    ],
    // server: {
    //     cors: true,
    //     origin: 'http://172.20.15.125:13000', // 根據你的後端運行的地址調整
    // },
});