import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

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
        render: h => h(App)
    }).$mount('#app')
})
