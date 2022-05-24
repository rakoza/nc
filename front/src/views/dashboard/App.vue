<template>
    <div>
        <!-- Hero -->
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        aplikacija.com
                    </h1>
                    <h2 class="subtitle">
                        Docker status
                    </h2>
                </div>
            </div>
        </section>

        <!-- Docker statuses -->
        <section class="section">
            <div class="container">
                <div class="columns">

                    <!-- Docker engine status -->
                    <div class="column">
                        <h1>1. Docker service</h1>
                        <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.docker"></b-icon>
                        <div :class="{'has-text-danger': status.docker !== 200, 'has-text-success': status.docker === 200}" v-else>
                            Docker service is {{ status.docker === 200 ? 'Up' : 'Down' }}
                        </div>
                        <div class="is-size-7 mt-3 is-italic" v-if="status.docker !== 200">
                            Install and run docker service by Linux commands:
                            <div class="has-text-info">systemctl start docker.socket</div>
                            <div class="has-text-info">systemctl start docker</div>
                        </div>
                    </div>

                    <!-- Nginx container -->
                    <div class="column">
                        <h1>2. Nginx docker container</h1>
                        <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.nginx"></b-icon>
                        <div :class="{'has-text-danger': status.nginx.state !== 'running', 'has-text-success': status.nginx.state === 'running'}" v-else>
                            {{ status.nginx.status }}
                        </div>
                        <div class="mt-3" v-if="status.nginx.state !== 'unknown'">
                            <b-button
                                v-if="status.nginx.state !== 'running'"
                                @click="startNginxContainer"
                                type="is-primary is-light"
                                icon-pack="fas"
                                icon-right="play"
                                :loading="isLoading.startNginx">
                                {{ $t('start') }}
                            </b-button>
                            <b-button
                                v-else
                                @click="stopNginxContainer"
                                type="is-danger"
                                icon-pack="fas"
                                icon-right="stop"
                                :loading="isLoading.stopNginx">
                                {{ $t('stop') }}
                            </b-button>
                        </div>
                    </div>

                    <div class="column">
                        <h1>3. Other docker containers</h1>
                        <b-icon icon="sync-alt" custom-class="fa-spin" v-if="isLoading.containers"></b-icon>
                        <div class="mt-3" v-else>
                            <table class="table is-size-6">
                                <tbody>
                                    <tr v-for="item in containers" :key="item.id">
                                        <td>{{ item.name }}</td>
                                        <td :class="{'has-text-danger': item.state !== 'running', 'has-text-success': item.state === 'running'}">
                                            {{ item.state }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>

export default {
    data() {
        return {
            status: {
                docker: 'Unknown',
                nginx: {
                    status: 'Unknown',
                    state: 'unknown',
                },
            },
            isLoading: {
                docker: true,
                nginx: false,
                startNginx: false,
                stopNginx: false,
                containers: false,
            },
            nginxId: null,
            containers: [],
        }
    },

    created() {
        this.fetchDockerStatus()
    },

    methods: {
        fetchDockerStatus() {
            this.$api.docker.status()
                .then(data => {
                    this.status.docker = data

                    if(data === 200) {
                        this.fetchNginxContainerStatus()
                        this.fetchContainers()
                    }
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.docker = false
                });
        },
        fetchNginxContainerStatus() {
            this.isLoading.nginx = true

            this.$api.docker.nginx()
                .then(data => {
                    this.status.nginx = data
                    this.nginxId = data.id
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.nginx = false
                });
        },
        fetchContainers() {
            this.isLoading.containers = true

            this.$api.docker.containers()
                .then(data => {
                    this.containers = data
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.containers = false
                });
        },
        startNginxContainer() {
            this.isLoading.startNginx = true

            this.$api.docker.startContainer(this.nginxId)
                .then(() => {
                    this.fetchNginxContainerStatus()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.startNginx = false
                });
        },
        stopNginxContainer() {
            this.isLoading.stopNginx = true

            this.$api.docker.stopContainer(this.nginxId)
                .then(() => {
                    this.fetchNginxContainerStatus()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading.stopNginx = false
                });
        },
    },
};
</script>
