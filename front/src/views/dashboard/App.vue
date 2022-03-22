<template>
    <div class="about">
        <h1>This is an protected page - DASHBOARD</h1>
        <button @click="logout()">Logout</button>
    </div>
</template>

<script>

export default {
    methods: {
        async logout()
        {
            try {
                await this.$store.dispatch('auth/signOut')
            } catch(e) {
                console.error(e.message)
            }
        },
    },
};
</script>
