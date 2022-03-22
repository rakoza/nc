import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

Vue.config.productionTip = false

axios.defaults.withCredentials = true

store.dispatch('auth/me').then(() => {
    new Vue({
        store,
        router,
        render: h => h(App)
    }).$mount('#app')
})
