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
                label="Email"
                :type="form.hasError('email')"
                :message="form.errorMessage('email')">
                <b-input
                    name="email"
                    v-model="form.email"
                    placeholder="Email">
                </b-input>
            </b-field>

            <!-- name -->
            <b-field
                class="required"
                label="Ime i prezime"
                :type="form.hasError('name')"
                :message="form.errorMessage('name')">
                <b-input
                    v-model="form.name"
                    name="name"
                    placeholder="Ime i prezime">
                </b-input>
            </b-field>

            <!-- password -->
            <b-field
                class="required" v-if="formMode == 'create'"
                label="Lozinka"
                :type="form.hasError('password')"
                :message="form.errorMessage('password')">
                <b-input
                    v-model="form.password"
                    name="password"
                    type="password"
                    placeholder="Lozinka">
                </b-input>
            </b-field>

            <!-- role_id -->
            <b-field
                v-if="isAdmin"
                expanded
                class="required"
                label="Rola"
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
                label="Telefon"
                :type="form.hasError('phone')"
                :message="form.errorMessage('phone')">
                <b-input
                    v-model="form.phone"
                    name="phone"
                    placeholder="Telefon">
                </b-input>
            </b-field>

            <!-- address -->
            <b-field
                label="Adresa"
                :type="form.hasError('address')"
                :message="form.errorMessage('address')">
                <b-input
                    v-model="form.address"
                    name="address"
                    placeholder="Adresa">
                </b-input>
            </b-field>

            <!-- notes -->
            <b-field
                label="Napomena"
                :type="form.hasError('notes')"
                :message="form.errorMessage('notes')">
                <b-input
                    type="textarea"
                    maxlength="1000"
                    v-model="form.notes"
                    name="notes"
                    placeholder="Napomena">
                </b-input>
            </b-field>

            <!-- active -->
            <b-field
                v-if="isAdmin"
                label="Status"
                :type="form.hasError('is_active')"
                :message="form.errorMessage('is_active')">
                <b-checkbox
                    name="is_active"
                    v-model="form.is_active"
                    :true-value="1"
                    :false-value="0">
                    {{ form.is_active ? 'Aktivan' : 'Neaktivan' }}
                </b-checkbox>
            </b-field>

        </section>

        <!--Card Footer-->
        <footer class="modal-card-foot" style="justify-content: space-between;">
            <div>
                <b-button @click="$parent.close()">Izađi</b-button>
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
            return this.formMode == 'create' ? 'Dodaj novog korisnika' : 'Promjeni podatke';
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

