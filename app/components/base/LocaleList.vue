<template>
  <div class="text-center">
    <v-menu open-on-hover bottom offset-y>
      <template v-slot:activator="{ on }">
        <v-btn
          min-width="0"
          text
          v-on="on"
        >
          <img :src="activeLocale.src" alt="">
        </v-btn>
      </template>

      <v-list>
        <v-list-item
          v-for="(item, index) in menuList"
          :key="index"
        >
          <v-list-item-title class="d-flex justify-center">
            <nuxt-link :to="switchLocalePath(item.lang)">
              <img :src="item.src" alt="">
            </nuxt-link>
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script>
export default {
  name: 'LocaleList',
  data () {
    return {
      menu: [
        { src: '/img/ico_lang_ru.png', lang: 'ru' },
        { src: '/img/ico_lang_en.png', lang: 'en' }
      ]
    }
  },

  computed: {
    menuList () {
      return this.menu.filter(val => val.lang !== this.$i18n.locale)
    },

    activeLocale () {
      return this.menu.find(val => val.lang === this.$i18n.locale)
    }
  }
}
</script>

<style lang="scss" scoped>
i {
  border: solid white;
  border-width: 0 0.2rem 0.2rem 0;
  display: inline-block;
  padding: 0.2rem;
  margin-left: 0.2rem;
}

.down {
  transform: rotate(45deg);
}
</style>
