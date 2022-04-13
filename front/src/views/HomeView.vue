<template>
  <div class="home">
    <img alt="Vue logo" src="../assets/logo.png">
    <router-link to="/">Go to Home</router-link> ||
    <router-link to="/login">Go to Login</router-link> ||
    <router-link to="/dashboard">Go to Dashboard (protected page)</router-link>
  </div>
</template>

<script>
export default {
  name: 'HomeView',
}
</script>
