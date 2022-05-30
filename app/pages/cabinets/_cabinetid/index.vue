<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  />
</template>

<script>
export default {
  layout: 'Cabinet',

  mounted () {
    this.$routeri18n.push('cabinets-cabinetid-dashboard')
  }
}
</script>

<style scoped>

</style>
