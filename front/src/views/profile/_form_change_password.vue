<template>
<!-- Form -->
<form @input="form.clearField($event)" >
    <div class="modal-card" style="width: 420px;">
        <header class="modal-card-head">
            <b-icon pack="fas" icon="key"></b-icon>
            <p class="modal-card-title ml-10">Promjena lozinke</p>
        </header>

        <section class="modal-card-body">
            <!-- current_password -->
            <b-field
                class="required"
                label="Tekuća lozinka"
                :type="form.hasError('current_password')"
                :message="form.errorMessage('current_password')">
                <b-input v-model="form.current_password"
                    name="current_password"
                    type="password"
                    placeholder="Tekuća lozinka"
                    password-reveal>
                </b-input>
            </b-field>

            <!-- password -->
            <b-field
                class="required"
                label="Nova lozinka"
                :type="form.hasError('password')"
                :message="form.errorMessage('password')">
                <b-input v-model="form.password"
                    name="password"
                    type="password"
                    placeholder="Nova lozinka"
                    password-reveal>
                </b-input>
            </b-field>

            <!-- password_confirmation -->
            <b-field
                class="required"
                label="Potvrda nove lozinke"
                :type="form.hasError('password_confirmation')"
                :message="form.errorMessage('password_confirmation')">
                <b-input v-model="form.password_confirmation"
                    name="password_confirmation"
                    type="password"
                    placeholder="Potvrda nove lozinke"
                    password-reveal>
                </b-input>
            </b-field>
        </section>

        <footer class="modal-card-foot" style="justify-content: space-between;">
            <!-- https://github.com/jgthms/bulma/issues/516 -->
            <div>
                <button class="button" type="button" @click="$parent.close">{{ $t('cancel') }}</button>
                <modal-button-save @click="submit()"></modal-button-save>
            </div>
        </footer>
    </div>

</form>
</template>

<script>

export default {
    name: 'UserPasswordForm',
    data() {
        const item = {
            current_password: null,
            password: null,
            password_confirmation: null,
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
        apiLink() {
            return '/pass';
        },
    },
    methods: {
        submit()
        {
            this.form
                .put('/pass')
                .then(() => {
                    this.$notify('Promjenjena je korisnička lozinka', 'is-success')
                })
                .catch(() => {})
        },
    }
};
</script>
