import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import i18n from './i18n'
import './buefy'

Vue.config.productionTip = false

axios.defaults.withCredentials = true

// ovdje se inicijalizuje nasa `single page` aplikacija
// pozivamo vuex akciju koja salje GET zahtjev na rutu /spa/check
// zavisno od http responsa code-a postavlja state.authenticated na true/false
// i kreira/mauntuje komponentu App.vue
store.dispatch('auth/me').then(() => {
    new Vue({
        store,
        router,
        i18n,
        render: h => h(App)
    }).$mount('#app')
})
