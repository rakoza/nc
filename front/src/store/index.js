import Vue from 'vue'
import Vuex from 'vuex'

// Vuex Pattern: Smart Module Registration
// https://dev.to/nkoik/-vuex-pattern-smart-module-registration-15gc
import modules from './modules'

const debug = process.env.NODE_ENV !== 'production'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {...modules},
    strict: debug,
})
