<template>
  <v-app dark>
    <v-app-bar
      absolute
      app
      dark
      height="75"
      color="transparent"
      :elevation="0"
    >
      <v-container class="d-flex align-center px-0 ">
        <v-toolbar-title
          class=" font-weight-light"
          v-text="$store.getters['user/currentUser'] ? $store.getters['user/currentUser'].name : ''"
        />
        <v-spacer />
        <BaseLocaleList />
        <v-btn v-if="$auth.isSuperAdmin()" text @click="$routeri18n.push('cabinets')">
          <v-icon>mdi-view-dashboard</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            Кабинеты
          </div>
        </v-btn>
        <v-btn v-if="$auth.isSuperAdmin()" text @click="$routeri18n.push('admin-dashboard')">
          <v-icon>mdi-google-analytics</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            Дашборд
          </div>
        </v-btn>
        <v-btn v-if="$auth.isSuperAdmin()" text @click="$routeri18n.push('admin-providers')">
          <v-icon>mdi-account-multiple</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            Поставщики
          </div>
        </v-btn>
        <v-btn v-if="$auth.isSuperAdmin()" text @click="$routeri18n.push('admin-billing')">
          <v-icon>mdi-currency-usd</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            Биллинг
          </div>
        </v-btn>
<!--        <v-btn v-if="$auth.isSuperAdmin()" text @click="$routeri18n.push('admin-ivr')">-->
<!--          <v-icon>mdi-phone</v-icon>-->
<!--          <div v-if="this.$vuetify.breakpoint.smAndUp">-->
<!--            IVR-->
<!--          </div>-->
<!--        </v-btn>-->
        <v-btn
          class="ml-2"
          min-width="0"
          text
          @click="$routeri18n.push('logout')"
        >
          <v-icon>mdi-arrow-right-thick</v-icon>
        </v-btn>
      </v-container>
    </v-app-bar>
    <v-main class="pa-0">
      <v-img
        src="https://images.unsplash.com/photo-1590431306482-f700ee050c59?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80"
        aspect-ratio="2"
        min-height="100vh"
        position="center"
        gradient="to top right, rgba(0,0,0,.5), rgba(0,0,0,.5)"
      >
        <v-responsive class="d-flex align-center py-16" min-height="100vh">
          <v-container class="fill-heigh justify-center">
            <nuxt />
          </v-container>
        </v-responsive>
      </v-img>
    </v-main>
    <v-footer
      absolute
      app
      dark
      height="75"
      color="transparent"
      :elevation="0"
    >
      <v-container class="d-flex align-center px-0 ">
        <v-spacer />
        <span>© 20 avodata.io</span>
      </v-container>
    </v-footer>
    <BaseMaterialSnackbar
      v-model="snackbar"
      :type="notifyColor"
      timeout="3000"
      v-bind="{
        top: true,
        right: true
      }"
    >
      {{ notifyText }}
    </BaseMaterialSnackbar>
  </v-app>
</template>

<script>
export default {
  name: 'Entrance',

  async fetch () {
    await this.$store.dispatch('user/getCurrentUser').then((res) => {
      this.$store.commit('user/setCurrentUser', res)
    }).catch(() => {
      this.$routeri18n.push('logout')
    })
  },

  data () {
    return {
      snackbar: false
    }
  },

  computed: {
    pageName () {
      return this.$store.getters.pageName
    },

    notify () {
      return this.$store.getters['notification/show']
    },

    notifyText () {
      return this.$store.getters['notification/text']
    },

    notifyColor () {
      return this.$store.getters['notification/color']
    }
  },

  watch: {
    notify (newVal) {
      this.snackbar = newVal
    },

    snackbar (newVal, oldVal) {
      if (newVal === false) {
        this.$store.commit('notification/clearNotify')
      }
    }
  },

  mounted () {
    this.$vuetify.theme.dark = true
  }
}
</script>
