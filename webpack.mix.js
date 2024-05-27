const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .js("resources/js/app_driver.js", "public/js")
    .js("resources/js/app_admin", "public/js")
    .js("resources/js/app_delivery_office", "public/js")
    .ts('resources/js/typescript.ts', 'public/js')
    // .js("resources/js/libs/Calendar/BaseCalendar", "public/js/test/Calendar")
    .sass("resources/sass/app_delivery_office.scss", "public/css")
    .sass("resources/sass/app_admin.scss", "public/css")
    .sass("resources/sass/app_driver.scss", "public/css")
    .sass("resources/sass/app_guest.scss", "public/css")
    .options({
        processCssUrls: false, // scss内で利用する画像などのパスをpublicから取り出す。
    })
    .sourceMaps(true, "inline-source-map") // scssのソースマップ表示
    .disableSuccessNotifications(); // 成功時の通知の制限
