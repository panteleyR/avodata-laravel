<template>
  <v-app light>
    <v-app-bar
      absolute
      color="white"
      elevate-on-scroll
      scroll-target="#scrolling-techniques-7"
    >
      <v-toolbar-side-icon class="mr-2">
        <img src="/avocado.png" alt="Vuetify.js" width="30">
      </v-toolbar-side-icon>
      <v-toolbar-title>Avodata.io</v-toolbar-title>

      <v-spacer />

      <v-toolbar-items>
        <v-btn text @click="$vuetify.goTo('.works', scrollOption)">
          Наша работа
        </v-btn>
        <v-btn text @click="$vuetify.goTo('.tariffs', scrollOption)">
          Тарифы
        </v-btn>
        <v-btn text @click="$vuetify.goTo('.interface', scrollOption)">
          Интерфейс
        </v-btn>
        <v-btn text @click="$vuetify.goTo('.contacts', scrollOption)">
          Контакты
        </v-btn>
        <v-btn text @click="$routeri18n.push('register')">
          <v-icon>mdi-backup-restore</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            Подключиться
          </div>
        </v-btn>
      </v-toolbar-items>
    </v-app-bar>
    <nuxt />
    <footer>
      <div class="footer">
        <div class="items">
          <div class="item">
            Политика конфиденциальности
          </div>
          <div class="item">
            Условия использования
          </div>
        </div>
        <div>
          @2022 avodata.io
        </div>
      </div>
    </footer>
  </v-app>
</template>

<script>
export default {
  data () {
    return {
      scrollOption: {
        duration: 300,
        offset: 0,
        easing: 'easeInOutCubic'
      }
    }
  }
}
</script>

<style scoped>
  footer {
     padding: 0 40px;
  }
  .footer {
    /*margin-top: 48px;*/
    padding: 32px 0;
    display: flex;
    justify-content: space-between;
    width: 100%;
    border-top: 1px solid #7f7f7f24;
  }
  .footer .items {
    display: flex;
  }
  .footer .item {
    margin-right: 24px;
    color: #7f7f7f;
  }
</style>
