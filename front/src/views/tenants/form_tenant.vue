<template>
    <div>
        <!-- Header -->
        <page-header :title="$tc('tenant')" :subtitle="subtitle">
        <!-- Buttons -->
            <div class="level-item buttons">
                <b-button
                    v-if="formMode === 'edit'"
                    @click="deleteTenant"
                    type="is-danger"
                    icon-pack="fas"
                    icon-right="trash">
                    {{ $t('delete') }}
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <!-- Form -->
            <form @input="form.clearField($event);" >
                <div v-if="isLoading">...</div>
                <div class="container" v-else>

                    <div class="columns">
                        <div class="column">
                            <!-- name -->
                            <b-field
                                class="required"
                                :label="$t('company')"
                                :type="form.hasError('name')"
                                :message="form.errorMessage('name')">
                                <b-input
                                    v-model="form.name"
                                    name="name"
                                    :placeholder="$t('company')">
                                </b-input>
                            </b-field>

                            <!-- email -->
                            <b-field
                                class="required"
                                :label="$t('email_address')"
                                :type="form.hasError('email')"
                                :message="form.errorMessage('email')">
                                <b-input
                                    name="email"
                                    v-model="form.email"
                                    :disabled="formMode === 'edit'"
                                    :placeholder="$t('email_address')">
                                </b-input>
                            </b-field>

                            <!-- domain -->
                            <b-field
                                :label="domainLabel"
                                :type="form.hasError('domain')"
                                :message="form.errorMessage('domain')">
                                <b-input
                                    expanded
                                    v-model="domain"
                                    name="domain"
                                    :placeholder="$t('domain')">
                                </b-input>
                                <p class="control has-background-light is-flex is-align-items-center">
                                    <span class="is-static px-3 has-text-weight-bold" v-if="isSubdomain">.{{ config.domain }}</span>
                                </p>
                            </b-field>

                            <!-- domain or subdomain -->
                            <b-field>
                                <b-checkbox v-model="theCustomersOwnDomain" type="is-warning">
                                    Check only if you want to use the costumer's own domain
                                </b-checkbox>
                            </b-field>

                            <!-- notes -->
                            <b-field
                                :label="$t('notes')"
                                :type="form.hasError('notes')"
                                :message="form.errorMessage('notes')">
                                <b-input
                                    type="textarea"
                                    maxlength="1000"
                                    v-model="form.notes"
                                    name="notes"
                                    :placeholder="$t('notes')">
                                </b-input>
                            </b-field>

                            <!-- active -->
                            <b-field
                                v-if="isAdmin"
                                :label="$t('is_active')"
                                :type="form.hasError('is_active')"
                                :message="form.errorMessage('is_active')">
                                <b-checkbox
                                    name="is_active"
                                    v-model="form.is_active"
                                    :true-value="1"
                                    :false-value="0">
                                    {{ form.is_active ? $t('yes') : $t('no') }}
                                </b-checkbox>
                            </b-field>
                        </div>

                        <div class="column">

                            <!-- src -->
                            <b-field
                                class="required"
                                :label="$t('software_application')"
                                :type="form.hasError('src')"
                                :message="form.errorMessage('src')">
                                <b-select
                                    expanded
                                    name="src"
                                    v-model="form.src">
                                    <option v-for="item in config.versions" :key="item" :value="item">
                                        {{ item }}
                                    </option>
                                </b-select>
                            </b-field>

                            <!-- timezone -->
                            <b-field
                                class="required"
                                :label="$t('timezone')"
                                :type="form.hasError('timezone')"
                                :message="form.errorMessage('timezone')">
                                <b-select
                                    expanded
                                    icon="globe"
                                    name="timezone"
                                    v-model="form.timezone">
                                    <option v-for="item in config.timezones" :key="item" :value="item">
                                        {{ item }}
                                    </option>
                                </b-select>
                            </b-field>

                            <!-- database -->
                            <b-field grouped>
                                <!-- db_host -->
                                <b-field
                                    class="required"
                                    :label="$t('database_server')"
                                    :type="form.hasError('db_host')"
                                    :message="form.errorMessage('db_host')">
                                    <b-input
                                        icon="database"
                                        v-model="form.db_host"
                                        name="db_host"
                                        :placeholder="$t('database_server')">
                                    </b-input>
                                </b-field>

                                <!-- db_username -->
                                <b-field
                                    expanded
                                    class="required"
                                    :label="$t('username')"
                                    :type="form.hasError('db_username')"
                                    :message="form.errorMessage('db_username')">
                                    <b-input
                                        icon="user"
                                        v-model="form.db_username"
                                        name="db_username"
                                        :placeholder="$t('username')">
                                    </b-input>
                                </b-field>

                                <!-- db_password -->
                                <b-field
                                    expanded
                                    class="required"
                                    :label="$t('password')"
                                    :type="form.hasError('db_password')"
                                    :message="form.errorMessage('db_password')">
                                    <b-input
                                        icon="key"
                                        v-model="form.db_password"
                                        name="db_password"
                                        :placeholder="$t('password')">
                                    </b-input>
                                </b-field>
                            </b-field>

                            <!-- redis_host -->
                            <b-field
                                class="required"
                                :label="$t('caching_server')"
                                :type="form.hasError('redis_host')"
                                :message="form.errorMessage('redis_host')">
                                <b-input
                                    icon="memory"
                                    v-model="form.redis_host"
                                    name="redis_host"
                                    :placeholder="$t('caching_server')">
                                </b-input>
                            </b-field>

                            <!-- word_to_pdf_worker -->
                            <b-field
                                class="required"
                                :label="$t('print_server')"
                                :type="form.hasError('word_to_pdf_worker')"
                                :message="form.errorMessage('word_to_pdf_worker')">
                                <b-input
                                    icon="print"
                                    v-model="form.word_to_pdf_worker"
                                    name="word_to_pdf_worker"
                                    :placeholder="$t('print_server')">
                                </b-input>
                            </b-field>

                            <!-- trial_period_end_date -->
                            <b-field
                                :label="$t('trial_period_end_date')"
                                :type="form.hasError('trial_period_end_date')"
                                :message="form.errorMessage('trial_period_end_date')">
                                <b-datepicker
                                    v-model="form.trial_period_end_date"
                                    locale="de-DE"
                                    placeholder="Click to select..."
                                    icon="calendar-day"
                                    :icon-right="form.trial_period_end_date ? 'times-circle' : ''"
                                    icon-right-clickable
                                    @icon-right-click="form.trial_period_end_date = null"
                                    trap-focus>
                                </b-datepicker>
                            </b-field>
                        </div>

                    </div>

                    <!--Card Footer-->
                    <footer class="is-flex is-flex-direction-row-reverse">
                        <div class="buttons">
                            <modal-button-cancel @click="$emit('close')"></modal-button-cancel>
                            <modal-button-save @click="submit()"></modal-button-save>
                        </div>
                    </footer>

                </div>
            </form>
            <!-- forma -->
        </section>
    </div>
</template>

<script>

export default {
    props: [],

    data() {
        const {db_host, timezone, redis_host, versions, word_to_pdf_worker} = this.$store.getters['auth/config']

        const randomString = () => { // https://stackoverflow.com/questions/10726909/random-alpha-numeric-string-in-javascript
          return Math.random().toString(36).slice(2)
        }

        const randomIntFromInterval = (min, max) => { // min and max included
          return Math.floor(Math.random() * (max - min + 1) + min)
        }

        let item = {
            db_username: 'user' + randomIntFromInterval(100000, 999999),
            db_password: randomString(),
            db_host,
            redis_host,
            word_to_pdf_worker,
            timezone,
            is_active: 1,
            trial_period_end_date: null,
            src: versions.length === 1 ? versions[0] : null,
        };

        const form = new this.$form(item)

        return {
            form,
            isLoading: true,
            theCustomersOwnDomain: false,
            domain: null, // domain or subdomain
        }
    },

    created() {

        if(this.formMode !== 'edit') {
            this.isLoading = false
            return
        }

        this.fetchTenant()
    },

    computed: {
        formMode() {
            return this.$route.query.form
        },
        subtitle() {
            return this.formMode === 'create'
                ? this.$tc('create')
                : this.$tc('edit')
        },
        isAdmin() {
            // return appconfig.userRole === 'administrator'
            return true
        },
        isSubdomain() {
            return !this.theCustomersOwnDomain
        },
        domainLabel() {
            return this.theCustomersOwnDomain
                ? this.$t('customers_internet_domain')
                : this.$t('subdomain')
        },
        config() {
            return this.$store.getters['auth/config']
        },
    },

    methods: {
        fetchTenant() {
            const id = this.$route.query.id

            this.$api.tenants.get(id)
                .then(({tenant}) => {
                    const item = this.$pick(tenant, [
                        'name',
                        'email',
                        // 'domain',
                        'notes',
                        'is_active',
                        'trial_period_end_date',
                        'db_host',
                        'db_username',
                        'db_password',
                        'redis_host',
                        'word_to_pdf_worker',
                        'timezone',
                        'src',
                    ]);


                    // ako je unesen "the customer's own domain"
                    // onda tenant.domain ne sadrzi string this.config.domain
                    if(tenant.domain.indexOf(this.config.domain) === -1) {
                        this.domain = tenant.domain
                        this.theCustomersOwnDomain = true
                    } else {
                        // podrazumjeva se upotreba poddomena
                        this.domain = tenant.domain.slice(0, -(this.config.domain.length+1))
                    }

                    this.form.setData(item)
                    // this.$forceUpdate()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    this.isLoading = false
                });
        },
        submit() {
            const url = this.formMode === 'create'
                ? '/tenants'
                : '/tenants/' + this.$route.query.id

            this.form.domain = this.domain

            if(this.isSubdomain) {
                this.form.domain = this.domain + '.' + this.config.domain
            }

            this.form
                .submit(this.formMode, url)
                .then(() => {
                    const message = this.formMode == 'create'
                        ? 'Novi zakupac je uspješno kreiran'
                        : 'Izmjene su uspješno sačuvane'

                    this.$notify(message, 'is-success')
                    this.$emit('data-saved')
                    this.$emit('close')
                })
                .catch(() => {})
        },
        deleteTenant() {
            const id = this.$route.query.id

            this.$api.tenants.delete(id)
                .then(() => {
                    this.$notify('Zakupac je izbrisan', 'is-success')
                    this.$emit('data-saved')
                    this.$emit('close')
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
        }
    }

};
</script>

