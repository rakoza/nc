<template>
  <div class="home">
    <img alt="Vue logo" src="../assets/logo.png">
    <HelloWorld msg="This is your Home Page"/>
    <router-link to="/">Go to Home</router-link> ||
    <router-link to="/login">Go to Login</router-link> ||
    <router-link to="/about">Go to About</router-link>
  </div>
</template>

<script>
// @ is an alias to /src
import HelloWorld from '@/components/HelloWorld.vue'

export default {
  name: 'HomeView',
  components: {
    HelloWorld
  }
}
</script>
