<template>
    <form-tenant
        v-if="showForm"
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
        @create-tenant="createTenant"
        @show-tenant="showTenant"
        @edit-tenant="editTenant"
        />
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
    },

    created() {
        this.reloadData()
    },

    methods: {
        reloadData() {
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
        closeForm() {
            this.$router.go(-1)
        },
        createTenant() {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'create',
                }
            })
        },
        editTenant(tenant) {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'edit',
                    id: tenant.id,
                }
            })
        },
        showTenant(tenant) {
            this.$router.push({
                name: 'tenants',
                query: {
                    form: 'show',
                    id: tenant.id,
                }
            })
        },
    },
};
</script>
