<template>
    <section>

        <div class="container is-fullhd">

            <div class="columns">
                <div class="column is-flex is-flex-direction-column is-justify-content-center p-6" style="min-height: 100vh;">

                    <!-- Login face -->
                    <div class="has-text-centered mb-5">
                        <img src="../assets/face.svg" width="150" height="150" alt="login-face">
                    </div>

                    <!-- Email -->
                    <b-field label="Email">
                        <b-input
                            type="email"
                            name="email"
                            ref="email"
                            size="is-medium"
                            v-model="email"
                            @keyup.enter="submitForm"
                            maxlength="50">
                        </b-input>
                    </b-field>

                    <!-- Password -->
                    <b-field label="Password">
                        <b-input
                            type="password"
                            name="password"
                            size="is-medium"
                            password-reveal
                            v-model="password"
                            @keyup.enter="submitForm">
                        </b-input>
                    </b-field>

                    <!-- Submit button -->
                    <b-button
                        class="mt-5"
                        size="is-medium"
                        type="is-primary"
                        @click="submitForm">
                        Submit
                    </b-button>
                </div>

                <!-- illustration -->
                <div class="column is-flex is-justify-content-center is-align-items-center p-6 is-hidden-mobile">
                    <img src="../assets/login.svg" alt="login">
                </div>
            </div>

        </div>
    </section>
</template>

<script>

export default {
    data: () => ({
        email: null,
        password: null,
        error: null,
        isLoggingIn: false,
    }),

    mounted() {
        this.$refs.email.focus()
    },

    computed: {
        env() {
            return process.env.NODE_ENV
        },
    },

    methods: {
        async submitForm() {
            // submit form to server/API here...
            const credentials = {
                email: this.email,
                password: this.password,
            }

            this.isLoggingIn = true

            try {
                await this.$store.dispatch('auth/signIn', credentials)
                this.error = null
                // this.$router.replace({ name: 'Home' })
            } catch(e) {
                console.error(e.message);
                this.error = e.message
            }

            this.isLoggingIn = false
        },
    },
};
</script>
