<template>
    <div>
        <!-- Header -->
        <page-header title="Korisnici">
            <!-- Buttons -->
            <div class="level-item buttons">
                <!-- isAdmin -->
                <div class="is-size-5 mr-3" v-if="!user">Za promjenu podataka selektuj korisnika u listi</div>
                <template v-else>
                <!-- Promjena lozinke -->
                <b-button
                    @click="changePassword"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="key">
                    Promjeni lozinku
                </b-button>

                <!-- Promjena lozinke -->
                <b-button
                    @click="editUser"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="edit">
                    Promjeni podatke
                </b-button>
                </template>

                <!-- Kreiranje novog korisnika -->
                <b-button
                    @click="createUser"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="user-plus">
                    Novi korisnik
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <div class="table-container">
                <table class="table is-fullwidth is-hoverable">
                    <thead>
                        <th style="width: 30px;"></th>
                        <th style="width: 150px;" v-if="isAdmin">Rola</th>
                        <th style="width: 250px;">Email</th>
                        <th style="width: 250px;">Ime i prezime</th>
                        <th style="width: 150px;">Telefon</th>
                        <th>Adresa</th>
                        <th style="width: 120px;" class="has-text-right">Kreiran</th>
                        <th style="width: 90px;" class="has-text-right" v-if="isAdmin">Aktivan</th>
                    </thead>

                    <tbody>
                        <tr v-for="(item,index) in users" :key="index" @click="selectUser(item)" @dblclick="editUser" :class="{'is-selected': item.id == selectedUserId}" class="pointer">
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
        selectedUserId() {
            return this.user ? this.user.id : null
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

                    // moramo anulirati izabranog korisnika, jer su to sada stari podaci u njemu
                    this.user = null
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
        editUser()
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormUser,
                hasModalCard: true,
                props: {
                    formMode: 'update',
                    roles: this.roles,
                    user: this.user,
                }, // Props to be binded to the injected component
                events: {
                    'data-saved': this.reloadData,
                }, // Events to be binded to the injected component
            });
        },
        changePassword()
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormPassword,
                hasModalCard: true,
                props: {
                    formMode: 'update',
                    user: this.user,
                }, // Props to be binded to the injected component
            });
        },
        selectUser(user)
        {
            // if(this.user && this.user.id == user.id) {
            //   this.user = null
            //   return
            // }

            this.user = user
        },
        roleIconType(user)
        {
            // mysql> select * from roles;
            // +----+---------------+---------------------+---------------------+
            // | id | name          | created_at          | updated_at          |
            // +----+---------------+---------------------+---------------------+
            // |  1 | administrator | 2021-11-14 09:43:22 | 2021-11-14 09:43:22 |
            // |  2 | editor        | 2021-11-14 09:43:22 | 2021-11-14 09:43:22 |
            // |  3 | approver      | 2021-11-14 09:43:22 | 2021-11-14 09:43:22 |
            // |  4 | volunteer     | 2022-01-12 08:29:28 | 2022-01-12 08:29:28 |
            // +----+---------------+---------------------+---------------------+

            if(user.role.id == 1) {
                // admin
                return 'is-danger';
            }
            if(user.role.id == 2) {
                // editor
                return 'is-warning';
            }
            if(user.role.id == 3) {
                // approver
                return 'is-info';
            }
            if(user.role.id == 4) {
                // volunteer
                return 'is-dark';
            }
        },
    },
};
</script>
