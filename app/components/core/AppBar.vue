<template>
  <v-app-bar
    id="app-bar"
    absolute
    app
    light
    height="69"
    style="border-bottom: 1px solid #e0e0e0;"
  >
    <v-btn
      class="mr-3"
      elevation="1"
      fab
      small
      @click="setDrawer(!drawer)"
    >
      <v-icon v-if="value">
        mdi-view-quilt
      </v-icon>

      <v-icon v-else>
        mdi-dots-vertical
      </v-icon>
    </v-btn>

    <v-toolbar-title
      class="font-weight-light"
      v-text="$store.getters['cabinet/currentCabinet'] ? $store.getters['cabinet/currentCabinet'].name + ' (' + $auth.getRoleName() + ')' : ''"
    />

    <v-spacer />

    <!--    <v-text-field-->
    <!--      :label="$t('search')"-->
    <!--      color="secondary"-->
    <!--      hide-details-->
    <!--      style="max-width: 165px;"-->
    <!--    >-->
    <!--      <template-->
    <!--        v-if="$vuetify.breakpoint.mdAndUp"-->
    <!--        v-slot:append-outer-->
    <!--      >-->
    <!--        <v-btn-->
    <!--          class="mt-n2"-->
    <!--          elevation="1"-->
    <!--          fab-->
    <!--          small-->
    <!--        >-->
    <!--          <v-icon>mdi-magnify</v-icon>-->
    <!--        </v-btn>-->
    <!--      </template>-->
    <!--    </v-text-field>-->

    <div class="mx-3" />

    <div v-if="false && $store.getters['cabinet/currentCabinet']" class="mr-2">
      Баланс: {{ $store.getters['cabinet/currentCabinet'].amount }}
    </div>
    <BaseLocaleList />

    <!--    <v-btn-->
    <!--      class="ml-2"-->
    <!--      min-width="0"-->
    <!--      text-->
    <!--      to="/"-->
    <!--    >-->
    <!--      <v-icon>mdi-view-dashboard</v-icon>-->
    <!--    </v-btn>-->

    <!--    <v-menu-->
    <!--      bottom-->
    <!--      left-->
    <!--      offset-y-->
    <!--      origin="top right"-->
    <!--      transition="scale-transition"-->
    <!--    >-->
    <!--      <template v-slot:activator="{ attrs, on }">-->
    <!--        <v-btn-->
    <!--          class="ml-2"-->
    <!--          min-width="0"-->
    <!--          text-->
    <!--          v-bind="attrs"-->
    <!--          v-on="on"-->
    <!--        >-->
    <!--          <v-badge-->
    <!--            color="red"-->
    <!--            overlap-->
    <!--            bordered-->
    <!--          >-->
    <!--            <template v-slot:badge>-->
    <!--              <span>5</span>-->
    <!--            </template>-->

    <!--            <v-icon>mdi-bell</v-icon>-->
    <!--          </v-badge>-->
    <!--        </v-btn>-->
    <!--      </template>-->

    <!--      <v-list-->
    <!--        :tile="false"-->
    <!--        nav-->
    <!--      >-->
    <!--        <div>-->
    <!--          <app-bar-item-->
    <!--            v-for="(n, i) in notifications"-->
    <!--            :key="`item-${i}`"-->
    <!--          >-->
    <!--            <v-list-item-title v-text="n" />-->
    <!--          </app-bar-item>-->
    <!--        </div>-->
    <!--      </v-list>-->
    <!--    </v-menu>-->
    <v-btn
      class="ml-2"
      min-width="0"
      text
      @click="$routeri18n.push('cabinets-cabinetid-profile')"
    >
      <v-icon>mdi-account</v-icon>
    </v-btn>
    <v-btn
      v-if="false"
      class="ml-2"
      min-width="0"
      text
      @click="$routeri18n.push('cabinets-cabinetid-settings')"
    >
      <v-icon>mdi-cogs</v-icon>
    </v-btn>
    <!--    <v-btn-->
    <!--      class="ml-2"-->
    <!--      min-width="0"-->
    <!--      text-->
    <!--    >-->
    <!--      <v-icon>mdi-help</v-icon>-->
    <!--    </v-btn>-->

    <v-btn
      class="ml-2"
      min-width="0"
      text
      @click="$routeri18n.push('cabinets')"
    >
      <v-icon>mdi-arrow-right-thick</v-icon>
    </v-btn>
  </v-app-bar>
</template>

<script>
// Components
// import { VHover, VListItem } from 'vuetify/lib'

// Utilities
import { mapState, mapMutations } from 'vuex'

export default {
  components: {
  },

  props: {
    value: {
      type: Boolean,
      default: false
    }
  },

  data: () => ({
    notifications: [
      'Mike John Responded to your email',
      'You have 5 new tasks',
      'You\'re now friends with Andrew',
      'Another Notification',
      'Another one'
    ]
  }),

  computed: {
    ...mapState(['drawer'])
  },

  methods: {
    ...mapMutations({
      setDrawer: 'SET_DRAWER'
    })
  }
}
</script>

<style scoped>
</style>
