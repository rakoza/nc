<template>
    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="">
                <p class="title">
                    Your very nice web site should appear here.
                </p>
                <p class="subtitle mt-6">
                    <router-link to="/login">Go to protected path of your app</router-link>
                    <!-- <router-link to="/dashboard">Go to Dashboard (protected page)</router-link> -->
                </p>
            </div>
        </div>
    </section>
</template>

<script>
export default {
  name: 'HomeView',
}
</script>
