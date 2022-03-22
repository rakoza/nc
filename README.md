## Laravel 9 + Vue CLI 5
Example config your Laravel project

## Steps for Scaffolding From Scratch
1. Create Laravel Project

   ``` sh
   laravel new my-project
   cd my-project
   # remove existing frontend scaffold
   rm -rf package.json webpack.mix.js resources/js resources/css
   ```

2. Create a Vue CLI 5 project
   ``` sh
   vue create front
   cd front
   ```

3. Configure Vue CLI 5 project

    Edit `/front/vue.config.js`:
    ``` js
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
            : 'index.html'
    })
    ```

    Edit `/resources/frontend/app/package.json`:
    ``` diff
    "scripts": {
      "serve": "vue-cli-service serve",
    - "build": "vue-cli-service build",
    + "build": "rm -rf ../../../public/assets/app/{js,css,img} && vue-cli-service build --no-clean",
      "lint": "vue-cli-service lint"
    },
    ```

4. Configure Laravel routes for SPA.

    **routes/web.php**

    ``` php
    Route::get('/', [HomeController::class, 'index']);

    // na produkciji moramo odgovoriti na refresh + /assets
    // tj povesti korisnika na spa index page
    Route::any('/{any}', [HomeController::class, 'index'])->where('any', '^(?!spa).*$');
    ```

    **app/Http/Controllers/FrontendController.php**

    ``` php
    <?php
    namespace App\Http\Controllers;
    use Illuminate\Http\Request;

    class HomeController extends Controller
    {

        /**
         * Index page
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            return view('index');
        }

    }
    ```

5. Add vue router
    ``` js
    vue add router
    ```

6. Change `base: process.env.BASE_URL` in `router.js` for correct Vue Router
    ``` js
    // For App
    base: '/',
    ```
