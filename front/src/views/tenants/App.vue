<template>
    <div>
        <!-- Header -->
        <page-header :title="title" :subtitle2="subtitle">
            <!-- Buttons -->
            <div class="level-item buttons">
                <span class="is-size-5 has-text-primary" v-if="subtitle">{{ subtitle }}</span>
                <!-- Kreiranje novog korisnika -->
                <b-button
                    v-if="!(showForm || showDetails)"
                    @click="createTenant"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="user-plus">
                    {{ $t('create') }}
                </b-button>
                <b-button
                    v-if="showForm && formMode === 'edit'"
                    @click="deleteTenant"
                    type="is-danger"
                    icon-pack="fas"
                    icon-right="trash">
                    {{ $t('delete') }}
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <form-tenant
                v-if="showForm"
                :formMode="formMode"
                @close="closeForm()"
                @data-saved="reloadData()"
                />

            <details-tenant
                v-else-if="showDetails"
                @close="closeForm()"
                @data-saved="reloadData()"
                />

            <table-tenants
                v-else
                :tenants="tenants"
                @show-tenant="showTenant"
                @edit-tenant="editTenant"
                />
        </section>

    </div>
</template>

<script>

import TableTenants from './table_tenants'
import FormTenant from './form_tenant'
import DetailsTenant from './details_tenant'

export default {
    components: {
        TableTenants,
        FormTenant,
        DetailsTenant,
    },

    data() {
        return {
            tenants: [],
        }
    },

    computed: {
        title() {
            return this.showForm || this.showDetails
                ? this.$tc('tenant')
                : this.$tc('tenant', 2)
        },
        subtitle() {
            if(!this.showForm) {
                return
            }

            return this.formMode === 'create'
                ? this.$tc('create')
                // : this.$tc('edit')
                : ''
        },
        isAdmin() {
            // return appconfig.userRole === 'administrator'
            return true
        },
        showForm() {
            return ['create','edit'].includes(this.$route.query.form)
        },
        showDetails() {
            return ['show'].includes(this.$route.query.form)
        },
        formMode() {
            return this.$route.query.form
        }
    },

    created() {
        this.reloadData()
    },

    methods: {
        reloadData()
        {
            this.$api.tenants.all()
                .then(data => {
                    this.tenants = data.tenants
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    // this.isLoading = false
                });
        },
        closeForm()
        {
            this.$router.go(-1)
        },
        createTenant()
        {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'create',
                }
            })
        },
        editTenant(tenant)
        {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'edit',
                    id: tenant.id,
                }
            })
        },
        showTenant(tenant)
        {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'show',
                    id: tenant.id,
                }
            })
        },
        deleteTenant()
        {
            const id = this.$route.query.id

            this.$api.tenants.delete(id)
                .then(() => {
                    this.$notify('Zakupac je izbrisan', 'is-success')
                    this.reloadData()
                    this.closeForm()
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
        }
    },
};
</script>
