const mix = require('laravel-mix');

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
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve('resources/sass')
            }
        }
    })
    .sass('resources/sass/app.scss', 'public/css')
    .version();

if (!mix.inProduction()) {
    mix.browserSync({
        notify: false,
        proxy: 'http://test.freenom.local',
        files: [
            'resources/js/*.js',
            'resources/js/*.vue',
            'resources/js/components/**/*.vue',
            'resources/js/environments/*.vue',
            'resources/js/router/*.js',
            'resources/sass/*.scss',
            'resources/sass/**/*.scss'
        ]
    });
}

if (mix.inProduction()) {
    mix.disableNotifications();
}
