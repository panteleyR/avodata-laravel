<template>
  <nuxt-child />
</template>

<script>
export default {
  layout: 'Cabinet',

  mounted () {
    if (!this.$cookies.get('access-token')) {
      this.$routeri18n.push('Login')
    }
  }
}
</script>

<style scoped>

</style>
