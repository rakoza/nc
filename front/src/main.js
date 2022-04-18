import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import i18n from './i18n'
import './buefy'
import { pick } from 'lodash'

import './vue-directives'
import './vue-filters'

import Form from './api/Form'
import api from './api/api'

Vue.config.productionTip = false

Vue.prototype.$api  = api
Vue.prototype.$pick = pick
Vue.prototype.$form = Form
Vue.prototype.$alertError = (message) => {
    // console.log(Vue.prototype.$buefy);
    Vue.prototype.$buefy.dialog.alert({
        title: 'Error',
        message: message,
        type: 'is-danger',
        hasIcon: true,
        icon: 'times-circle',
        iconPack: 'fa'
    })
}
Vue.prototype.$notify = (text, type) => {
    Vue.prototype.$buefy.toast.open({
        message: text,
        type: type,
        queue: false,
        duration: 5000,
    })
}
/* ---------------------------------------------------------
 * Globally registered VueJS components
 * ---------------------------------------------------------
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 */
const files = require.context('./components', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/* ---------------------------------------------------------
 * App Entry Point
 * ---------------------------------------------------------
 * ovdje se inicijalizuje nasa `single page` aplikacija
 * pozivamo vuex akciju koja salje GET zahtjev na rutu /spa/check
 * zavisno od http responsa code-a postavlja state.authenticated na true/false
 * i kreira/mauntuje komponentu App.vue
 */
store.dispatch('auth/me').then(() => {
    new Vue({
        store,
        router,
        i18n,
        render: h => h(App)
    }).$mount('#app')
})
