<template>
<!-- Form -->
<form @input="form.clearField($event);" >
    <div class="modal-card">
        <!-- Card Header -->
        <header class="modal-card-head">
            <p class="modal-card-title">
                <span class="fas fa-user-lock mr-10"></span>
                {{ title }}
            </p>
        </header>

        <!--Card Body-->
        <section class="modal-card-body">

            <!-- password -->
            <b-field
                class="required"
                :label="$t('new_password')"
                :type="form.hasError('password')"
                :message="form.errorMessage('password')">
                <b-input
                    type="password"
                    name="password"
                    v-model="form.password"
                    :placeholder="$t('new_password')">
                </b-input>
            </b-field>

            <!-- password_confirmation -->
            <b-field
                class="required"
                :label="$t('confirm_new_password')"
                :type="form.hasError('password_confirmation')"
                :message="form.errorMessage('password_confirmation')">
                <b-input
                    type="password"
                    name="password_confirmation"
                    v-model="form.password_confirmation"
                    :placeholder="$t('confirm_new_password')">
                </b-input>
            </b-field>

        </section>

        <!--Card Footer-->
        <footer class="modal-card-foot">
            <div>
                <modal-button-cancel @click="$parent.close()"></modal-button-cancel>
                <modal-button-save @click="submit()"></modal-button-save>
            </div>
        </footer>
    </div>
</form>
<!-- forma -->
</template>

<script>

export default {
    props: ['user','formMode'],

    data() {
        const form = new this.$form()
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
            return this.$t('password_change')
        },
    },

    methods: {
        submit()
        {
            const url = '/update-user-password/' + this.user.id

            this.form
                .submit(this.formMode, url)
                .then(() => {
                    this.$notify('Promjenjena je korisnička lozinka', 'is-success')
                })
                .catch(() => {})

        }
    },
};
</script>

