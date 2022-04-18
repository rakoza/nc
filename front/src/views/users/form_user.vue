<template>
<!-- Form -->
<form @input="form.clearField($event);" >
    <div class="modal-card">
        <!-- Card Header -->
        <header class="modal-card-head">
            <p class="modal-card-title">
                <b-icon :icon="formMode === 'create' ? 'user-plus' : 'pen'" class="mr-1" />
                {{ title }}
            </p>
        </header>

        <!--Card Body-->
        <section class="modal-card-body">

            <!-- name -->
            <!-- <b-field
                class="required"
                label="Name"
                :type="form.hasError('name')"
                :message="form.errorMessage('name')">
                <b-input
                    name="name"
                    v-model="form.name"
                    placeholder="Name">
                </b-input>
            </b-field> -->

            <!-- email -->
            <b-field
                v-if="formMode === 'create'"
                class="required"
                :label="$t('email_address')"
                :type="form.hasError('email')"
                :message="form.errorMessage('email')">
                <b-input
                    name="email"
                    v-model="form.email"
                    :placeholder="$t('email_address')">
                </b-input>
            </b-field>

            <!-- name -->
            <b-field
                class="required"
                :label="$t('full_name')"
                :type="form.hasError('name')"
                :message="form.errorMessage('name')">
                <b-input
                    v-model="form.name"
                    name="name"
                    :placeholder="$t('full_name')">
                </b-input>
            </b-field>

            <!-- password -->
            <b-field
                class="required" v-if="formMode == 'create'"
                :label="$t('password')"
                :type="form.hasError('password')"
                :message="form.errorMessage('password')">
                <b-input
                    v-model="form.password"
                    name="password"
                    type="password"
                    :placeholder="$t('password')">
                </b-input>
            </b-field>

            <!-- role_id -->
            <b-field
                v-if="isAdmin"
                expanded
                class="required"
                :label="$t('role')"
                :type="form.hasError('role_id')"
                :message="form.errorMessage('role_id')">
                <b-select expanded
                    name="role_id"
                    v-model="form.role_id">
                    <option v-for="item in roles" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </b-select>
            </b-field>

            <!-- phone -->
            <b-field
                :label="$t('telephone_number')"
                :type="form.hasError('phone')"
                :message="form.errorMessage('phone')">
                <b-input
                    v-model="form.phone"
                    name="phone"
                    :placeholder="$t('telephone_number')">
                </b-input>
            </b-field>

            <!-- address -->
            <b-field
                :label="$t('address')"
                :type="form.hasError('address')"
                :message="form.errorMessage('address')">
                <b-input
                    v-model="form.address"
                    name="address"
                    :placeholder="$t('address')">
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

        </section>

        <!--Card Footer-->
        <footer class="modal-card-foot is-justify-content-space-between">
            <div>
                <modal-button-cancel @click="$parent.close()"></modal-button-cancel>
                <modal-button-save @click="submit()"></modal-button-save>
            </div>
            <div v-if="formMode == 'update'">
                <modal-button-delete @click="deleteItem()"></modal-button-delete>
            </div>
        </footer>
    </div>
</form>
<!-- forma -->
</template>

<script>

export default {
    props: ['user','roles','formMode'],

    data() {
        let item = {
            'is_active': 1,
        };

        if(this.formMode == 'update') {
            item = this.$pick(this.user, [
                'name',
                'email',
                'role_id',
                'is_active',
                'phone',
                'address',
                'notes',
            ]);
        }

        const form = new this.$form(item)
        form.onSuccessCallback = () => {
            this.$emit('data-saved');
            this.$parent.close();
        }

        return {
            form,
        }
    },

    computed: {
        title() {
            return this.formMode == 'create' ? this.$t('add_new_user') : this.$t('edit');
        },
        role() {
            // izabrana rola
            return this.roles.find(item => item.id === this.form.role_id)
        },
        isUser() {
            if(!this.role) {
                // ako nismo izabrali rolu
                return false
            }

            // korisnik kojem mijenjamo podatke ima rolu `user`
            return this.role.name === 'user'
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
                ? '/users'
                : '/users/' + this.user.id

            this.form
                .submit(this.formMode, url)
                .then(() => {
                    const message = this.formMode == 'create'
                        ? 'Novi korisnik je uspješno kreiran'
                        : 'Izmjene su uspješno sačuvane'

                    this.$notify(message, 'is-success')
                })
                .catch(() => {})
        },
        deleteItem()
        {
            this.form
                .delete('/users/' + this.user.id)
                .then(() => {
                    this.$notify('Korisnik je izbrisan', 'is-success')
                })
                .catch(() => {})
        }
    }

};
</script>

