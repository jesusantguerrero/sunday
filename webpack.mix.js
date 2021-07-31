const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
.sass('resources/css/app.scss', 'public/css').options({
    postCss: [
        require('postcss-import'),
        require('tailwindcss'),
    ]
})
.webpackConfig(require('./webpack.config'))
