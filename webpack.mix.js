const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js').vue()
.sass('resources/css/app.scss', 'public/css').options({
    postCss: [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]
})
.webpackConfig(require('./webpack.config'))


if (mix.inProduction()) {
    mix.version();
}
