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

mix.js('resources/js/main.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();

mix.copy('node_modules/fullcalendar/main.js', 'public/js/fullcalendar.js')
mix.copy('node_modules/fullcalendar/main.css', 'public/css/fullcalendar.css')
mix.copy('node_modules/fullcalendar/locales/sk.js', 'public/js/fullcalendar-sk.js')
