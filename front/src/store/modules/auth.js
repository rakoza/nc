import axios from 'axios'

export default {
    namespaced: true,

    state: {
        authenticated: false,
        user: null,
        config: null,
    },

    getters: {
        authenticated (state) {
            return state.authenticated
        },

        user (state) {
            return state.user
        },

        config (state) {
            return state.config
        },
    },

    mutations: {
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },

        SET_USER (state, value) {
            state.user = value
        },

        SET_CONFIG (state, value) {
            state.config = value
        },
    },

    actions: {
        async signIn ({ dispatch }, credentials) {
            await axios.get('/spa/csrf-cookie')
            await axios.post('/spa/login', credentials)

            return dispatch('me')
        },

        async signOut ({ dispatch }) {
            await axios.post('/spa/logout')

            return dispatch('me')
        },

        me ({ commit }) {
            return axios.get('/spa/check')
                .then(response => {
                    commit('SET_AUTHENTICATED', true)

                    const {user, config} = response.data
                    commit('SET_USER', user)
                    commit('SET_CONFIG', config)
                }).catch(() => {
                    commit('SET_AUTHENTICATED', false)
                    commit('SET_USER', null)
                    commit('SET_CONFIG', null)
                })
        }
    }
}
