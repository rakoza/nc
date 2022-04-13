<template>
    <div id="app" v-if="!isLoading">
        <main-menu v-if="isAuthenticated"></main-menu>

        <transition name="fade" mode="out-in">
            <router-view />
        </transition>
    </div>
</template>

<script>

// @ is an alias to /src
import MainMenu from '@/components/MainMenu'

// console.log(process.env);

export default {

    components: {
        MainMenu,
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
