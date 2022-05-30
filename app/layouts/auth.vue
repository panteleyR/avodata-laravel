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
          v-text="'avodata.io'"
        />
        <v-spacer />
        <v-btn v-if="getRouteBaseName() !== 'login'" text @click="$routeri18n.push('login')">
          <v-icon>mdi-arrow-right-thick</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            {{ $t('auth.login') }}
          </div>
        </v-btn>
        <v-btn v-if="getRouteBaseName() !== 'register'" text @click="$routeri18n.push('register')">
          <v-icon>mdi-account-multiple-plus</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            {{ $t('register') }}
          </div>
        </v-btn>
        <v-btn v-if="getRouteBaseName() !== 'recovery'" text @click="$routeri18n.push('recovery')">
          <v-icon>mdi-backup-restore</v-icon>
          <div v-if="this.$vuetify.breakpoint.smAndUp">
            {{ $t('auth.password-recovery') }}
          </div>
        </v-btn>
        <BaseLocaleList />
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
  name: 'Auth',

  data () {
    return {
      snackbar: false
    }
  },

  computed: {
    pageName () {
      return this.$store.getters.pageName
    },
    // notify: {
    //   get () {
    //     return this.$store.getters['notification/notify']
    //   },
    //   set (val) {
    //     console.log(val)
    //     // this.$store.commit('notification/changeShow', val)
    //     if (val === false) {
    //       this.$store.commit('notification/clearNotify')
    //     }
    //   }
    // }
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
