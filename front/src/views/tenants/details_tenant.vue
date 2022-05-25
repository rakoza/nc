<template>
    <div>
        <!-- Header -->
        <page-header :title="$tc('tenant')" :subtitle="$t('details')">
            <!-- Buttons -->
            <div class="level-item buttons" v-if="!isLoading.docker">
                <template v-if="ifContainerIsNotCreated">
                    <b-button
                        @click="createContainer"
                        type="is-primary"
                        icon-pack="fas"
                        icon-right="cog"
                        :loading="isLoading.create">
                        {{ $t('create_docker_container') }}
                    </b-button>
                </template>
                <template v-else-if="docker.state !== 'running'">
                    <b-button
                        @click="removeContainer"
                        type="is-danger is-light"
                        icon-pack="fas"
                        icon-right="trash"
                        :loading="isLoading.remove">
                        {{ $t('destroy') }}
                    </b-button>
                    <b-button
                        @click="startContainer"
                        type="is-success"
                        icon-pack="fas"
                        icon-right="play"
                        :loading="isLoading.start">
                        {{ $t('start') }}
                    </b-button>
                </template>
                <b-button
                    v-else
                    @click="stopContainer"
                    type="is-danger"
                    icon-pack="fas"
                    icon-right="stop"
                    :loading="isLoading.stop">
                    {{ $t('stop') }}
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <div v-if="isLoading.tenant">...</div>
            <div class="container" v-else>

                <div class="columns">
                    <div class="column">
                        <!-- name -->
                        <b-field :label="$t('name2')">
                            {{ tenant.name }}
                            <span class="is-size-7 has-text-grey">(id: {{ tenant.id }})</span>
                        </b-field>
                        <!-- email -->
                        <b-field :label="$t('email_address')">
                            {{ tenant.email }}
                        </b-field>
                        <!-- domain -->
                        <b-field :label="$t('domain')">
                            <a :href="'http://' + tenant.domain" target="_blank">
                                {{ tenant.domain }}
                                <i class="fas fa-link"></i>
                            </a>
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
                        <div v-if="isLoading.docker">...</div>
                        <div v-else>
                            <!-- name -->
                            <b-field :label="$t('status')" :class="{'has-text-danger': docker.state !== 'running', 'has-text-success': docker.state === 'running'}">
                                {{ docker.status || 'not created'}}
                            </b-field>
                        </div>

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

        </section>
    </div>
</template>

<script>

export default {
    props: [],

    data() {
        return {
            tenant: null,
            isLoading: {
                tenant: true,
                docker: true,
                start: false,
                stop: false,
                create: false,
            },
            docker: {
                status: 'unknown',
            },
        }
    },

    created() {
        this.getTenantDetails()
    },

    computed: {
        isAdmin() {
            // return appconfig.userRole === 'administrator'
            return true
        },
        ifContainerIsNotCreated() {
            return typeof this.docker.state === 'undefined'
        },
    },

    methods: {
        getTenantDetails() {
            const id = this.$route.query.id

            this.$api.tenants.get(id)
                .then(({tenant}) => {
                    this.tenant = tenant

                    this.fetchContainer()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.tenant = false
                });
        },
        fetchContainer() {
            const id = this.$route.query.id

            this.$api.docker.tenant(id)
                .then(docker => {
                    this.docker = docker
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.docker = false
                });
        },
        startContainer() {
            this.isLoading.start = true

            this.$api.docker.startContainer(this.docker.id)
                .then(() => {
                    this.fetchContainer()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.start = false
                });
        },
        stopContainer() {
            this.isLoading.stop = true

            this.$api.docker.stopContainer(this.docker.id)
                .then(() => {
                    this.fetchContainer()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.stop = false
                });
        },
        createContainer() {
            this.isLoading.create = true

            this.$api.docker.createContainer(this.tenant.id)
                .then(() => {
                    this.fetchContainer()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.create = false
                });
        },
        removeContainer() {
            const runRemoveContainer = () => {

                this.isLoading.remove = true

                this.$api.docker.removeContainer(this.docker.id)
                    .then(() => {
                        this.$buefy.toast.open({
                            duration: 3000,
                            message: 'Docker kontejner je izbrisan',
                            type: 'is-danger',
                            queue: false
                        })

                        this.fetchContainer()
                    })
                    .catch(error => {
                        this.$alertError(error.message);
                        // throw error
                    })
                    .finally(() => {
                        this.isLoading.remove = false
                    });
            }

            this.$buefy.dialog.confirm({
                title: this.$t('destroy'),
                message: this.$t('remove_docker_confirmation'),
                confirmText: this.$t('yes'),
                cancelText: this.$t('cancel'),
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => runRemoveContainer()
            })
        },
    }

};
</script>

