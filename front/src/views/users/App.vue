<template>
    <div>
        <!-- Header -->
        <page-header :title="title">
            <!-- Buttons -->
            <div class="level-item buttons">
                <!-- Kreiranje novog korisnika -->
                <b-button
                    @click="createUser"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="user-plus">
                    {{ $t('add_new_user') }}
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <div class="table-container">
                <table class="table is-fullwidth is-hoverable">
                    <thead>
                        <th style="width: 30px;"></th>
                        <th style="width: 150px;" v-if="isAdmin">{{ $t('role') }}</th>
                        <th style="width: 250px;">{{ $t('email_address') }}</th>
                        <th style="width: 250px;">{{ $t('full_name') }}</th>
                        <th style="width: 150px;">{{ $t('telephone') }}</th>
                        <th>{{ $t('address') }}</th>
                        <th style="width: 120px;" class="has-text-right">{{ $t('created_at') }}</th>
                        <th style="width: 90px;" class="has-text-right" v-if="isAdmin">{{ $t('is_active') }}</th>
                        <th></th>
                    </thead>

                    <tbody>
                        <tr v-for="(item,index) in users" :key="index">
                            <td>{{ index + 1 }}.</td>
                            <td v-if="isAdmin">
                                <b-icon :icon="item.is_active ? 'user' : 'user-slash'" size="is-small" :type="roleIconType(item)"></b-icon>
                                {{ item.role.name }}
                            </td>
                            <td>{{ item.email }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.phone }}</td>
                            <td>{{ item.address }}</td>
                            <td class="has-text-right">{{ item.created_at | dd_mm_yyyy }}</td>
                            <td class="has-text-right"
                                :class="{'has-text-danger has-text-weight-bold': !item.is_active}"
                                v-if="isAdmin">{{ item.is_active ? 'Da' : 'Ne' }}</td>
                            <td>
                                <div class="is-flex is-flex-wrap-nowrap is-justify-content-flex-end">
                                    <!-- Promjena lozinke -->
                                    <b-button
                                        v-if="isAdmin"
                                        @click="changePassword(item)"
                                        class="mr-2"
                                        type="is-primary"
                                        icon-pack="fas"
                                        icon-right="key">
                                        <!-- {{ $t('change_password') }} -->
                                    </b-button>

                                    <!-- Promjena lozinke -->
                                    <b-button
                                        v-if="isAdmin"
                                        @click="editUser(item)"
                                        type="is-primary"
                                        icon-pack="fas"
                                        icon-right="edit">
                                        <!-- {{ $t('edit') }} -->
                                    </b-button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</template>

<script>

import FormUser from './form_user'
import FormPassword from './form_user_pass'

export default {
    data() {
        return {
            user: null, // izabrani user
            users: [],
            roles: [],
        }
    },

    computed: {
        title() {
            return this.$tc('user', 2)
        },
        isAdmin() {
            // return appconfig.userRole === 'administrator'
            return true
        },
    },

    created() {
        this.reloadData()
    },

    methods: {
        reloadData()
        {
            this.$api.users.all()
                .then(data => {
                    this.users = data.users
                    this.roles = data.roles
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    // this.isLoading = false
                });
        },
        createUser()
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormUser,
                hasModalCard: true,
                props: {
                    formMode: 'create',
                    roles: this.roles,
                }, // Props to be binded to the injected component
                events: {
                    'data-saved': this.reloadData,
                }, // Events to be binded to the injected component
            });
        },
        editUser(user)
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormUser,
                hasModalCard: true,
                props: {
                    formMode: 'update',
                    roles: this.roles,
                    user,
                }, // Props to be binded to the injected component
                events: {
                    'data-saved': this.reloadData,
                }, // Events to be binded to the injected component
            });
        },
        changePassword(user)
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormPassword,
                hasModalCard: true,
                props: {
                    formMode: 'update',
                    user,
                }, // Props to be binded to the injected component
            });
        },
        roleIconType(user)
        {
            // mysql> select * from roles;
            // +----+---------------+---------------------+---------------------+
            // | id | name          | created_at          | updated_at          |
            // +----+---------------+---------------------+---------------------+
            // |  1 | administrator | 2021-11-14 09:43:22 | 2021-11-14 09:43:22 |
            // |  2 | user          | 2021-11-14 09:43:22 | 2021-11-14 09:43:22 |
            // +----+---------------+---------------------+---------------------+

            if(user.role.id == 1) {
                // admin
                return 'is-danger';
            }
            if(user.role.id == 2) {
                // user
                return 'is-info';
            }
        },
    },
};
</script>
