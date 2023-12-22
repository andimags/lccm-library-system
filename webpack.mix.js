const mix = require("laravel-mix");
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");

mix.js("resources/js/app.js", "public/js")
    .sass("resources/sass/atlantis.scss", "public/css/atlantis.css")
    .version();

// mix.browserSync("127.0.0.1:8000");
