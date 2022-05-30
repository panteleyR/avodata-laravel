<template>
  <div />
</template>

<script>
export default {
  name: 'Logout',
  layout: 'auth',

  mounted () {
    this.$axios.$post('/logout', {})
    this.$cookies.set('access-token', '')
    this.$routeri18n.push('login')
  }
}
</script>

<style scoped>

</style>
