const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')
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

mix
    .js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css','public/css')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })

    .disableNotifications()

if (mix.inProduction()) {
    mix
        // .extract() // Disabled until resolved: https://github.com/JeffreyWay/laravel-mix/issues/1889
        .version()
} else {
    mix.sourceMaps()
}
