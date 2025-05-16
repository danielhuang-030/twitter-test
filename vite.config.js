import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { config as dotenvConfig } from 'dotenv';

dotenvConfig();

export default defineConfig({
  // 統一將所有資源前綴為 /build/
  base: '/build/',

  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
      // Laravel Vite 插件會從 public/build/manifest.json 讀取映射
      buildDirectory: 'build',
      refresh: true,
    }),
    vue(),
  ],

  server: {
    host: '0.0.0.0',
    port: parseInt(process.env.VITE_DEV_PORT) || 5173,
    hmr: {
      // HMR 也會以 /build/ 為基礎但仍指向 Dev Server
      host: new URL(process.env.APP_URL).hostname,
    },
  },

  build: {
    // 1. 編譯輸出到 public/build
    outDir: 'public/build',
    // 2. 刪除舊檔
    emptyOutDir: true,
    // 3. 生成 manifest.json（放在 public/build/manifest.json）
    manifest: 'manifest.json',
    // 4. 所有靜態資產置於 public/build/assets
    assetsDir: 'assets',

    // 5. 壓縮配置
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
    },

    rollupOptions: {
      output: {
        // 6. 帶 hash 的檔名規則
        entryFileNames:   'assets/[name].[hash].js',
        chunkFileNames:   'assets/[name].[hash].js',
        assetFileNames:   'assets/[name].[hash][extname]',
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return id
              .toString()
              .split('node_modules/')[1]
              .split('/')[0];
          }
        },
      },
    },

    chunkSizeWarningLimit: 1600,
  },
});
