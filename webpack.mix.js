const mix = require('laravel-mix');
const path = require('path');

mix.js("resources/js/app.js", "public").vue()
    .setPublicPath("public")
    .postCss("resources/css/app.css", "public", [require("tailwindcss")])
    .webpackConfig({
        resolve: {
            fallback: {
                fs: false,
                constants: false,
                stream: false,
                path: false,
            },
            alias: {
                // 'vue': "vue/dist/vue.js",
                '@': path.resolve('resources/js'),
            },
        },
    })
    .copyDirectory("public", "../../public/lava")