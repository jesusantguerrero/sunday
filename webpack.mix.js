const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js').postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
])
.webpackConfig(require('./webpack.config'))
.browserSync({
    proxy: 'http://localhost:8080',
    port: 5000
});
