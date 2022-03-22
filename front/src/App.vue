<template>
    <div id="app" v-if="!isLoading">
        <protected-view v-if="isAuthenticated" />
        <public-view v-else />
    </div>
</template>

<script>

import PublicView from './views/PublicView'
import ProtectedView from './views/ProtectedView'

// console.log(process.env);

export default {
    components: {
        PublicView,
        ProtectedView,
    },

    data() {
        return {
            // Ako povlacim neke podatke prije nego sto cu prikazati stranicu
            isLoading: false,
        }
    },

    computed: {
        isAuthenticated() {
            return this.$store.getters['auth/authenticated']
        }
    },

    mounted() {
        this.fetchInitData()

        // ovo je situacija kad je korisik prijavljen, a proba poci na rute home ili login
        if(this.isAuthenticated === true && ['home','login'].includes(this.$route.name)) {
            // samo ga vratimo na dashboard
            this.$router.replace({ name: 'dashboard' })
        }
    },

    methods: {
        fetchInitData() {
            // preFetch common data
            // this.$api.app.initSession()
            //     .then(data => {
            //         // store data to vuex
            //         this.storeInitData(data)
            //     })
            //     .catch(error => this.$alertError(error.message))
            //     .finally(() => {
            //         this.isLoading = false
            //     });
        },

        // storeInitData(data) {
        //     this.$store.commit('setSession', {
        //         locale: data.locale,
        //         translations: data.translations,
        //     });
        // },
    },

    watch: {
        // prijava i odjava
        isAuthenticated(to) {
            if(to === false) {
                this.$router.replace({ name: 'home' })
            } else {
                this.$router.replace({ name: 'dashboard' })
            }
        }
    }
};
</script>
