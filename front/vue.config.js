const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
    transpileDependencies: true,

    devServer: {
        host: 'nc.local',
        // When devServer.proxy is set to a string, only XHR requests will be proxied.
        // proxy: 'http://nc.local',
        // @see: https://github.com/starkovsky/laravel-vue-cli
        proxy: {
            '/spa': {
                target: 'http://nc.local',
            },
            // staticki asseti su u public/static
            // logo, images i slicne stvari
            '/static': {
                target: 'http://nc.local',
            }
        },
    },

    lintOnSave: process.env.NODE_ENV !== 'production',
    productionSourceMap: false,

    css: {
        sourceMap: false
    },

    // output built static files to Laravel's public dir.
    // note the "build" script in package.json needs to be modified as well.
    outputDir: '../public/assets',

    publicPath: process.env.NODE_ENV === 'production'
        ? '/assets/'
        : '/',

    // modify the location of the generated HTML file.
    // Specify the output path for the generated index.html (relative to outputDir). Can also be an absolute path.
    indexPath: process.env.NODE_ENV === 'production'
        ? '../../resources/views/index.blade.php'
        : 'index.html',

    // added by i18n vue-cli plugin
    pluginOptions: {
      i18n: {
        locale: 'hr',
        fallbackLocale: 'en',
        localeDir: 'locales',
        enableInSFC: false,
        enableBridge: false
      }
    },

    // How to change the page title from the default template
    // https://cli.vuejs.org/config/#pages
    pages: {
        index: {
            // entry for the page
            entry: 'src/main.js',
            // the source template
            template: 'public/index.html',
            // output as dist/index.html
            filename: 'index.html',
            // when using title option,
            // template title tag needs to be <title><%= htmlWebpackPlugin.options.title %></title>
            title: 'Your very nice app',
            // chunks to include on this page, by default includes
            // extracted common chunks and vendor chunks.
            chunks: ['chunk-vendors', 'chunk-common', 'index']
        },
        // when using the entry-only string format,
        // template is inferred to be `public/subpage.html`
        // and falls back to `public/index.html` if not found.
        // Output filename is inferred to be `subpage.html`.
        // subpage: 'src/subpage/main.js'
    }
})
