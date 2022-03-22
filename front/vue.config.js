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
        ? '/assets/app/'
        : '/',

    // modify the location of the generated HTML file.
    // Specify the output path for the generated index.html (relative to outputDir). Can also be an absolute path.
    indexPath: process.env.NODE_ENV === 'production'
        ? '../../../resources/views/app.blade.php'
        : 'index.html'
})
