<template>
  <v-app light>
    <CoreAppBar />
    <CoreDrawer />
    <CoreView />
    <CoreSettings />
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
  name: 'Cabinet',

  async fetch () {
    await this.$store.dispatch('user/getCurrentUser').then((res) => {
      this.$store.commit('user/setCurrentUser', res)
    }).catch(() => {
      this.$routeri18n.push('logout')
    })

    if (this.$route.params.cabinetid) {
      if (!this.$auth.isSuperAdmin()) {
        await this.$store.dispatch('member/getCurrentMember', { cabinetid: this.$route.params.cabinetid }).then((res) => {
          this.$store.commit('member/setCurrentMember', res)
        }).catch(() => {
          this.$routeri18n.push('cabinets')
        })
      }

      await this.$store.dispatch('cabinet/getCurrentCabinet', { cabinetid: this.$route.params.cabinetid }).then((res) => {
        this.$store.commit('cabinet/setCurrentCabinet', res)
      }).catch(() => {
        this.$routeri18n.push('cabinets')
      })
    }
  },

  data () {
    return {
      expandOnHover: false,
      snackbar: false
    }
  },

  computed: {
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
    this.$vuetify.theme.dark = false
  }
}
</script>

<style scoped lang="sass">
.v-main__wrap
  padding-bottom: 6rem
</style>
