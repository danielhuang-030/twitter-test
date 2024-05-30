const mix = require('laravel-mix');

if (!mix.inProduction()) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
    mix.webpackConfig({
        plugins: [
            new BundleAnalyzerPlugin({
                analyzerMode: 'server',
                analyzerHost: '0.0.0.0',
                analyzerPort: 9090 // 確保這個端口在你的伺服器上是開放的
            })
        ]
    });
}

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
