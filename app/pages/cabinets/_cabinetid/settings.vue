<template>
  <v-container>
    <v-row>
      <v-col
        cols="12"
      >
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="pb-0 display-2 font-weight-light">
                Настройка кабинета
              </v-card-title>
            </v-col>
          </template>

          <v-card-text>
            <div class="overline mb-4">
              {{ $store.getters['cabinet/currentCabinet'].name }}
            </div>
            <div>Баланс: {{ $store.getters['cabinet/currentCabinet'].amount }}</div>
          </v-card-text>
        </BaseMaterialCard>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'Settings'
}
</script>

<style scoped>

</style>
