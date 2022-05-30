<template>
  <BaseMaterialCard
    class="v-card--material-chart"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <template v-slot:customHeading>
      <BarChart
        :data="data"
        :options="options"
        style="position: relative; height: 200px; width: 100%"
      />
    </template>

    <slot
      slot="reveal-actions"
      name="reveal-actions"
    />

    <slot />

    <slot
      slot="actions"
      name="actions"
    />
  </BaseMaterialCard>
</template>

<script>
import BarChart from '~/components/BarChart'

export default {
  components: {
    BarChart
  },

  inheritAttrs: false,

  props: {
    data: {
      type: Object,
      default: () => ({})
    },
    eventHandlers: {
      type: Array,
      default: () => ([])
    },
    options: {
      type: Object,
      default: () => ({})
    },
    ratio: {
      type: String,
      default: undefined
    },
    type: {
      type: String,
      required: true,
      validator: v => ['Bar', 'Line', 'Pie'].includes(v)
    }
  }
}
</script>

<style lang="sass">
  .v-card--material-chart
    p
      color: #999

    .v-card--material__heading
      max-height: 280px
      canvas
        width: 100%!important
        height: 100% !important

      .ct-label
        color: inherit
        opacity: .7
        font-size: 0.975rem
        font-weight: 100

      .ct-grid
        stroke: rgba(255, 255, 255, 0.2)

      .ct-series-a .ct-point,
      .ct-series-a .ct-line,
      .ct-series-a .ct-bar,
      .ct-series-a .ct-slice-donut
          stroke: rgba(255,255,255,.8)

      .ct-series-a .ct-slice-pie,
      .ct-series-a .ct-area
          fill: rgba(255,255,255,.4)
</style>
