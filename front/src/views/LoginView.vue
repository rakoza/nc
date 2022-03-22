<template>
    <section>
        <h1>Login form</h1>

        <!-- Email -->
        <label for="name">Email:</label>
        <input
            type="text"
            name="email"
            ref="email"
            size="is-large"
            v-model="email"
            @keyup.enter="submitForm"
            />
        <br>

        <!-- Password -->
        <label for="password">Password:</label>
        <input
            type="password"
            name="password"
            v-model="password"
            @keyup.enter="submitForm"
            />
        <br>


        <button @click="submitForm">
            Submit
        </button>
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
