<template>
  <nuxt-child />
</template>

<script>
export default {
  name: 'Admin',
  layout: 'Entrance',

  mounted () {
    if (!this.$cookies.get('access-token') || !this.$auth.isSuperAdmin()) {
      this.$routeri18n.push('Login')
    }
  }
}
</script>

<style scoped>

</style>
