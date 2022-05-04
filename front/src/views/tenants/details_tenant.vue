<template>
<div v-if="isLoading">...</div>
<div class="container" v-else>

    <div class="columns">
        <div class="column">
            <!-- name -->
            <b-field :label="$t('name2')">
                {{ tenant.name }}
            </b-field>
            <!-- email -->
            <b-field :label="$t('email_address')">
                {{ tenant.email }}
            </b-field>
            <!-- domain -->
            <b-field :label="$t('domain')">
                {{ tenant.domain }}
            </b-field>
            <!-- notes -->
            <b-field :label="$t('notes')">
                {{ tenant.notes }}
            </b-field>
            <!-- is_active -->
            <b-field :label="$t('is_active')">
                {{ tenant.is_active ? $t('yes') : $t('no') }}
            </b-field>
            <!-- db_host -->
            <b-field :label="$t('database_server')">
                {{ tenant.db_host }}
            </b-field>
            <!-- db_username -->
            <b-field :label="$t('username')">
                {{ tenant.db_username }}
            </b-field>
            <!-- db_password -->
            <b-field :label="$t('password')">
                {{ tenant.db_password }}
            </b-field>
            <!-- redis_host -->
            <b-field :label="$t('caching_server')">
                {{ tenant.redis_host }}
            </b-field>
            <!-- timezone -->
            <b-field :label="$t('timezone')">
                {{ tenant.timezone }}
            </b-field>
            <!-- src -->
            <b-field :label="$t('version')">
                {{ tenant.src }}
            </b-field>
            <!-- trial_period_end_date -->
            <b-field :label="$t('trial_period_end_date')">
                {{ tenant.trial_period_end_date | dd_mm_yyyy }}
            </b-field>
        </div>

        <div class="column">

        </div>

    </div>

    <!--Card Footer-->
    <!-- <footer class="is-flex is-flex-direction-row-reverse">
        <div class="buttons">
            <modal-button-cancel @click="$emit('close')"></modal-button-cancel>
            <modal-button-save @click="submit()"></modal-button-save>
        </div>
    </footer> -->

</div>
</template>

<script>

export default {
    props: [],

    data() {
        return {
            tenant: null,
            isLoading: true,
        }
    },

    created() {
        const id = this.$route.query.id

        this.$api.tenants.get(id)
            .then(({tenant}) => {
                this.tenant = tenant
            })
            .catch(error => {
                this.$alertError(error.message);
                throw error
            })
            .finally(() => {
                this.isLoading = false
            });
    },

    computed: {
        isAdmin() {
            // return appconfig.userRole === 'administrator'
            return true
        },
    },

    methods: {
        //
    }

};
</script>

