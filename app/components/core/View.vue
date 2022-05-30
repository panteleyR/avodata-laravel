<template>
  <v-content id="dashboard-main">
    <transition name="fade">
      <nuxt />
    </transition>
    <CoreFooter />
  </v-content>
</template>

<script>
export default {
}
</script>

<style>
#dashboard-main .v-main__wrap{
  padding-bottom: 6rem!important;
}
</style>
