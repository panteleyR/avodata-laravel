<template>
  <v-navigation-drawer
    id="core-navigation-drawer"
    v-model="drawer"
    :expand-on-hover="expandOnHover"
    :right="$vuetify.rtl"
    color="white"
    flat
    mobile-breakpoint="960"
    app
    width="230"
    v-bind="$attrs"
    class="pa-0"
  >
    <template v-slot:img="props">
      <v-img
        v-bind="props"
      />
    </template>

    <v-divider class="mb-1" />

    <v-list
      dense
      nav
    >
      <v-list-item>
        <v-list-item-avatar
          class="align-self-center"
          color="white"
          contain
        >
          <v-img
            src="/avocado.png"
            max-height="45"
            contain
          />
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title
            class="display-2 custom-title"
            v-text="profile.title"
          />
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <v-divider class="mb-2" />

    <v-list
      expand
      nav
    >
      <!-- Style cascading bug  -->
      <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
      <div />

      <template v-for="(item, i) in computedItems">
        <BaseItemGroup
          v-if="item.children"
          :key="`group-${i}`"
          :item="item"
        >
          <!--  -->
        </BaseItemGroup>

        <BaseItem
          v-else
          :key="`item-${i}`"
          :item="item"
        />
      </template>
      <v-list-item
        :href="'/api/documentation'"
        :replace="true"
        :class="`black--text`"
        :active-class="`base-item-active primary white--text`"
      >
        <v-list-item-icon class="black--text">
          <v-icon color="black" v-text="'mdi-api'" />
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title v-text="'Документация'" />
        </v-list-item-content>
      </v-list-item>

      <!-- Style cascading bug  -->
      <!-- https://github.com/vuetifyjs/vuetify/pull/8574 -->
      <div />
    </v-list>

    <template v-slot:append>
      <!--      <BaseItem-->
      <!--        :item="{-->
      <!--          title: $t('upgrade'),-->
      <!--          icon: 'mdi-package-up',-->
      <!--          to: '/upgrade',-->
      <!--        }"-->
      <!--      />-->
      <div />
    </template>
  </v-navigation-drawer>
</template>

<script>
import { mapState } from 'vuex'

export default {
  props: {
    expandOnHover: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      itemss: [
        {
          title: 'dashboard',
          icon: 'mdi-view-dashboard',
          to: 'admin-dashboard'
        },
        {
          title: 'menu.users',
          icon: 'mdi-account',
          to: 'admin-users'
        },
        {
          title: 'menu.domains',
          icon: 'mdi-clipboard-outline',
          to: 'admin-domains'
        }
        // {
        //   title: 'rtables',
        //   icon: 'mdi-clipboard-outline',
        //   to: '/tables/regular-tables'
        // },
        // {
        //   title: 'typography',
        //   icon: 'mdi-format-font',
        //   to: '/components/typography'
        // },
        // {
        //   title: 'icons',
        //   icon: 'mdi-chart-bubble',
        //   to: '/components/icons'
        // },
        // {
        //   title: 'google',
        //   icon: 'mdi-map-marker',
        //   to: '/maps/google-maps'
        // },
        // {
        //   title: 'notifications',
        //   icon: 'mdi-bell',
        //   to: '/components/notifications'
        // }
      ]
    }
  },

  computed: {
    ...mapState(['barColor', 'barImage']),
    drawer: {
      get () {
        return this.$store.state.drawer
      },
      set (val) {
        this.$store.commit('SET_DRAWER', val)
      }
    },

    items () {
      return [
        {
          title: 'dashboard',
          icon: 'mdi-view-dashboard',
          to: 'cabinets-cabinetid-dashboard'
        },
        {
          title: 'menu.users',
          icon: 'mdi-account',
          to: 'cabinets-cabinetid-users'
        },
        {
          title: 'menu.domains',
          icon: 'mdi-web',
          to: 'cabinets-cabinetid-domains'
        }
      ]
    },

    computedItems () {
      return this.items.filter((val) => {
        // if (val.title === 'menu.users') {
        //   return !this.$auth.isAnalytic()
        // }
        //
        if (val.title === 'menu.domains') {
          return true
        }

        return true
      }).map(this.mapItem)
    },

    profile () {
      return {
        avatar: true,
        title: this.$t('avatar')
      }
    }
  },

  methods: {
    mapItem (item) {
      return {
        children: item.children ? item.children.map(this.mapItem) : undefined,
        title: this.$t(item.title),
        icon: item.icon,
        to: item.to
      }
    }
  }
}
</script>

<style lang="sass">
  @import '~vuetify/src/styles/tools/_rtl.sass'
  .custom-title
    line-height: 2rem!important
    font-weight: 400

  #core-navigation-drawer
    .v-list-group__header.v-list-item--active:before
      opacity: .24
    .base-item-active
      background: #195798!important
    .v-list .v-list-item--active .v-icon
      color: white!important
    .v-sheet.v-list
      padding: 0
    .v-list--nav .v-list-item, .v-list--nav .v-list-item:before
      border-radius: 0
    .v-list-item
      &__icon--text,
      &__icon:first-child
        justify-content: center
        text-align: center
        width: 20px

        +ltr()
          margin-right: 24px
          margin-left: 12px !important

        +rtl()
          margin-left: 24px
          margin-right: 12px !important

    .v-list--dense
      .v-list-item
        &__icon--text,
        &__icon:first-child
          margin-top: 10px

    .v-list-group--sub-group
      .v-list-item
        +ltr()
          padding-left: 8px

        +rtl()
          padding-right: 8px

      .v-list-group__header
        +ltr()
          padding-right: 0

        +rtl()
          padding-right: 0

        .v-list-item__icon--text
          margin-top: 19px
          order: 0

        .v-list-group__header__prepend-icon
          order: 2

          +ltr()
            margin-right: 8px

          +rtl()
            margin-left: 8px
</style>
