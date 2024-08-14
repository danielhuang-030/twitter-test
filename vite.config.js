import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { config as dotenvConfig } from 'dotenv';

dotenvConfig();

// 獲取 APP_URL 並移除端口部分
const appUrl = process.env.APP_URL;
const url = new URL(appUrl);
const hostWithoutPort = url.hostname;

export default defineConfig({
    base: '/build/',
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: '0.0.0.0',
        port: parseInt(process.env.VITE_DEV_PORT) || 12002,
        hmr: {
            host: hostWithoutPort,
        },
    },
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                // 正式環境時移除 console
                drop_console: true,
                drop_debugger: true
            }
        },
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name].[hash][extname]',
                chunkFileNames: 'assets/[name].[hash].js',
                entryFileNames: 'assets/[name].[hash].js',
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id
                            .toString()
                            .split('node_modules/')[1]
                            .split('/')[0]
                            .toString();
                    }
                }
            }
        },
        chunkSizeWarningLimit: 1600,
    },
});