<template>
    <div>
        <!-- Header -->
        <page-header :title="$tc('tenant')" :subtitle="$t('details')">
            <!-- Buttons -->
            <div class="level-item buttons" v-if="!isLoading.docker">
                <!-- <template v-if="ifContainerIsNotCreated">
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
                </b-button> -->
            </div>
        </page-header>

        <section class="section">
            <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.tenant"></b-icon>
            <div class="container" v-else>

                <div class="columns">
                    <div class="column">
                        <div class="is-size-5 has-text-grey-light mb-3">
                            <i class="fas fa-user"></i>
                            Tenant
                        </div>
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
                        <!-- <b-field :label="$t('domain')">
                            <a :href="'http://' + tenant.domain" target="_blank">
                                {{ tenant.domain }}
                                <i class="fas fa-link"></i>
                            </a>
                        </b-field> -->
                        <!-- notes -->
                        <b-field :label="$t('notes')">
                            {{ tenant.notes }}
                        </b-field>
                        <!-- is_active -->
                        <b-field :label="$t('is_active')">
                            {{ tenant.is_active ? $t('yes') : $t('no') }}
                        </b-field>
                        <!-- db_host -->
                        <!-- <b-field :label="$t('database_server')">
                            {{ tenant.db_host }}
                        </b-field> -->
                        <!-- db_username -->
                        <!-- <b-field :label="$t('username')">
                            {{ tenant.db_username }}
                        </b-field> -->
                        <!-- db_password -->
                        <!-- <b-field :label="$t('password')">
                            {{ tenant.db_password }}
                        </b-field> -->
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

                    <!-- domain -->
                    <div class="column">
                        <div class="is-size-5 has-text-grey-light mb-3">
                            <i class="fab fa-docker"></i>
                            Docker container
                        </div>
                        <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.docker"></b-icon>
                        <div v-else>
                            <!-- name -->
                            <b-field :label="$t('status')" :class="{'has-text-danger': docker.state !== 'running', 'has-text-success': docker.state === 'running'}">
                                {{ docker.status || 'not created'}}
                            </b-field>
                            <!-- docker buttons -->
                            <div class="buttons">
                                <template v-if="ifContainerIsNotCreated">
                                    <b-button
                                        @click="createContainer"
                                        type="is-primary"
                                        size="is-small is-rounded"
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
                                        size="is-small is-rounded"
                                        icon-pack="fas"
                                        icon-right="trash"
                                        :loading="isLoading.remove">
                                        {{ $t('destroy') }}
                                    </b-button>
                                    <b-button
                                        @click="startContainer"
                                        type="is-success"
                                        size="is-small is-rounded"
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
                                    size="is-small is-rounded"
                                    icon-pack="fas"
                                    icon-right="stop"
                                    :loading="isLoading.stop">
                                    {{ $t('stop') }}
                                </b-button>
                            </div>
                        </div>
                    </div>

                    <!-- database -->
                    <div class="column">
                        <div class="is-size-5 has-text-grey-light mb-3">
                            <i class="fas fa-database"></i>
                            Database
                        </div>
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
                    </div>

                    <!-- internet domain -->
                    <div class="column">
                        <div class="is-size-5 has-text-grey-light mb-3">
                            <i class="fas fa-globe"></i>
                            Internet domain
                        </div>
                        <b-field :label="$t('domain')">
                            <a :href="'http://' + tenant.domain" target="_blank">
                                {{ tenant.domain }}
                                <i class="fas fa-link"></i>
                            </a>
                        </b-field>
                        <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.domain"></b-icon>
                        <div v-else>
                            <!-- name -->
                            <b-field :label="$t('status')">
                                <div class="has-text-danger" v-if="domain.status !== 'ok'">
                                    {{ domain.error_message }}
                                </div>
                                <div v-else>
                                    <div class="has-text-success has-text-weight-bold">OK</div>
                                    <div class="is-size-7">name: {{ domain.record.domain.name }}</div>
                                    <div class="is-size-7">ttl: {{ domain.record.domain.ttl }}</div>
                                    <!-- <div class="is-size-7">zone file: {{ domain.record.domain.zone_file }}</div> -->
                                </div>
                            </b-field>
                            <b-button
                                v-if="domain.status !== 'ok' && domain.error_id === 'not_found'"
                                @click="createDomain"
                                type="is-primary is-light"
                                size="is-small is-rounded"
                                icon-pack="fas"
                                icon-right="plus"
                                :loading="isLoading.createDomain">
                                {{ $t('create') }}
                            </b-button>
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
                domain: false,
                createDomain: false,
            },
            docker: {
                status: 'unknown',
            },
            domain: {
                status: 'unknown',
                data: null,
                error: null,
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
                    this.fetchDomainRecord()
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
        fetchDomainRecord()
        {
            this.isLoading.domain = true

            this.$api.digitalocean.domain(this.tenant.id)
                .then(data => {
                    // console.log(data)

                    // primjer - greska
                    // {"status":"error","error_id":"not_found","error_message":"The resource you were accessing could not be found."}
                    this.domain = data
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.domain = false
                });
        },
        createDomain()
        {
            this.isLoading.createDomain = true

            this.$api.digitalocean.createDomain(this.tenant.id)
                .then(() => {
                    // console.log(data)
                    this.fetchDomainRecord()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.createDomain = false
                });
        }
    }

};
</script>

