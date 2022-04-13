<template>
    <b-navbar class="is-dark no-print" wrapper-class="container is-fluid">
        <!-- Logo -->
        <template #brand>
            <b-navbar-item class="has-text-info" tag="router-link" :to="{ path: '/' }">
                <img src="../assets/logo.png" alt="Logo" />
            </b-navbar-item>
        </template>

        <!-- Left menu -->
        <template #start>
            <!-- Main menu -->
            <template v-for="(item, index) in routes">
                <!-- If the item has subroutes -->
                <b-navbar-dropdown right collapsible
                    v-if="item.subroutes"
                    :key="index">
                    <template #label>
                        <b-icon :icon="item.icon" size="is-medium" class="mr-1" v-if="item.icon"></b-icon>
                        {{ item.name }}
                    </template>
                    <!-- Subitems -->
                    <template v-for="(subitem, subindex) in item.subroutes">
                        <b-navbar-item
                            :key="index + '-' + subindex"
                            :active="currentRoutePath == subitem.link"
                            tag="router-link"
                            :to="{ path: subitem.link }">
                            <b-icon :icon="subitem.icon" size="is-medium" class="mr-1" v-if="subitem.icon"></b-icon>
                            {{ subitem.name }}
                        </b-navbar-item>
                    </template>
                </b-navbar-dropdown>

                <!-- else -->
                <b-navbar-item
                    v-else
                    :key="index"
                    :active="currentRoutePath == item.link"
                    tag="router-link"
                    :to="{ path: item.link }">
                    <b-icon :icon="item.icon" size="is-medium" class="mr-1" v-if="item.icon"></b-icon>
                    {{ item.name }}
                </b-navbar-item>
            </template>
        </template>

        <!-- Right menu -->
        <template #end>

            <!-- Select language -->
            <b-navbar-dropdown right collapsible>
                <template #label>
                    <b-icon icon="globe-americas" size="is-medium" class="mr-1"></b-icon>
                    {{ $tc('language') }} ({{ $i18n.locale.toUpperCase() }})
                </template>

                <!-- Languages -->
                <b-navbar-item
                    v-for="loc in locales"
                    :key="loc"
                    :active="$i18n.locale == loc"
                    :hreflang="loc"
                    href="#"
                    @click.prevent="switchLocale(loc)">
                    {{ $t('language_' + loc) }}
                    <!-- {{ key.toUpperCase() }} - {{ loc.name }} / {{ loc.native }} -->
                </b-navbar-item>
            </b-navbar-dropdown>


            <!-- Profile -->
            <b-navbar-dropdown right collapsible>
                <template #label>
                    <i class="fas fa-fw fa-lg fa-user-circle mr-1"></i>
                    {{ $tc("profile") }}
                </template>

                <!-- Profile photo or avatar icon -->
                <b-navbar-item tag="div" class="is-size-5 has-text-grey-light">
                    <!-- <figure class="image is-32x32 mr-10" v-if="profile.photo">
                        <img class="is-rounded" style="max-height: 100%;" :src="profile.photo">
                    </figure> -->
                    <!-- <i class="fas fa-2x fa-fw fa-user-circle mr-10" v-else></i> -->
                    <!-- {{ user.getFullName() }} -->
                    Predrag Rakonjac
                </b-navbar-item>

                <hr class="navbar-divider">

                <b-navbar-item
                    tag="router-link"
                    :to="{ path: '/profile' }">
                    <i class="fas fa-fw fa-lg fa-user-cog mr-1"></i>
                    {{ $t('edit_profile') }}
                </b-navbar-item>
                <!-- <b-navbar-item @click.prevent="changePassword">
                    <i class="fas fa-lg fa-fw fa-key mr-10"></i>
                    {{ $t('change_password') }}
                </b-navbar-item> -->

                <hr class="navbar-divider">

                <!-- Sign out -->
                <b-navbar-item @click.prevent="logout()">
                    <i class="fas fa-fw fa-lg fa-sign-out-alt mr-1"></i>
                    {{ $t('sign_out') }}
                </b-navbar-item>

            </b-navbar-dropdown>

        </template>
    </b-navbar>
</template>

<script>
export default {
    data() {
        return {
            selectedIndex: -1,
            locales: ['en', 'hr'],
        };
    },
    methods: {
        switchLocale(locale) {
            if (this.$i18n.locale !== locale) {
                // window.moment.locale(locale)
                this.$i18n.locale = locale;
            }
        },
        async logout() {
            try {
                await this.$store.dispatch('auth/signOut')
            } catch(e) {
                console.error(e.message)
            }
        },
    },
    computed: {
        currentRoutePath() {
            return this.$route.path
        },
        // user() {
        //     return User
        // },
        routes() {
            return [
                {
                    name: this.$tc("tenant", 2),
                    link: "/tenants",
                    icon: "users",
                    // subroutes: [
                    //     {
                    //         name: this.$tc("distinct_part", 2),
                    //         link: "/admin/distinct-parts",
                    //         icon: "building",
                    //     },
                    // ]
                },
            ];
        },
    },
};
</script>
