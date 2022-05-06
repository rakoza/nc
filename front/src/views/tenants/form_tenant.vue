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
                                :label="$t('name2')"
                                :type="form.hasError('name')"
                                :message="form.errorMessage('name')">
                                <b-input
                                    v-model="form.name"
                                    name="name"
                                    :placeholder="$t('name2')">
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
                                :label="$t('domain')"
                                :type="form.hasError('domain')"
                                :message="form.errorMessage('domain')">
                                <b-input
                                    v-model="form.domain"
                                    name="domain"
                                    :placeholder="$t('domain')">
                                </b-input>
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
                            <!-- db_host -->
                            <b-field
                                class="required"
                                :label="$t('database_server')"
                                :type="form.hasError('db_host')"
                                :message="form.errorMessage('db_host')">
                                <b-input
                                    v-model="form.db_host"
                                    name="db_host"
                                    :placeholder="$t('database_server')">
                                </b-input>
                            </b-field>

                            <!-- db_username -->
                            <b-field
                                class="required"
                                :label="$t('username')"
                                :type="form.hasError('db_username')"
                                :message="form.errorMessage('db_username')">
                                <b-input
                                    v-model="form.db_username"
                                    name="db_username"
                                    :placeholder="$t('username')">
                                </b-input>
                            </b-field>

                            <!-- db_password -->
                            <b-field
                                class="required"
                                :label="$t('password')"
                                :type="form.hasError('db_password')"
                                :message="form.errorMessage('db_password')">
                                <b-input
                                    v-model="form.db_password"
                                    name="db_password"
                                    :placeholder="$t('password')">
                                </b-input>
                            </b-field>

                            <!-- redis_host -->
                            <b-field
                                class="required"
                                :label="$t('caching_server')"
                                :type="form.hasError('redis_host')"
                                :message="form.errorMessage('redis_host')">
                                <b-input
                                    v-model="form.redis_host"
                                    name="redis_host"
                                    :placeholder="$t('caching_server')">
                                </b-input>
                            </b-field>

                            <!-- timezone -->
                            <b-field
                                class="required"
                                :label="$t('timezone')"
                                :type="form.hasError('timezone')"
                                :message="form.errorMessage('timezone')">
                                <b-input
                                    v-model="form.timezone"
                                    name="timezone"
                                    :placeholder="$t('timezone')">
                                </b-input>
                            </b-field>

                            <!-- src -->
                            <b-field
                                class="required"
                                :label="$t('version')"
                                :type="form.hasError('src')"
                                :message="form.errorMessage('src')">
                                <b-input
                                    v-model="form.src"
                                    name="src"
                                    :placeholder="$t('version')">
                                </b-input>
                            </b-field>


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
        let item = {
            is_active: 1,
            trial_period_end_date: null,
        };

        const form = new this.$form(item)

        return {
            form,
            isLoading: true,
        }
    },

    created() {
        if(this.formMode !== 'edit') {
            this.isLoading = false
            return
        }

        const id = this.$route.query.id

        this.$api.tenants.get(id)
            .then(({tenant}) => {
                const item = this.$pick(tenant, [
                    'name',
                    'email',
                    'domain',
                    'notes',
                    'is_active',
                    'trial_period_end_date',
                    'db_host',
                    'db_username',
                    'db_password',
                    'redis_host',
                    'timezone',
                    'src',
                ]);

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
    },

    methods: {
        submit()
        {
            const url = this.formMode === 'create'
                ? '/tenants'
                : '/tenants/' + this.$route.query.id

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
        deleteTenant()
        {
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

