<template>
  <div>
    {{ min }} : {{ sec }}
  </div>
</template>

<script>
export default {
  name: 'Timer',

  props: {
    minProp: {
      type: Number,
      default: 5
    },
    secProp: {
      type: String,
      default: '00'
    }
  },

  data () {
    return {
      min: 5,
      sec: '00'
    }
  },

  watch: {
    minProp (newVal) {
      console.log(newVal)
      this.min = newVal
    },
    secProp (newVal) {
      this.sec = newVal
    }
  },

  mounted () {
    this.min = this.minProp
    this.sec = this.secProp
    this.timer()
  },

  methods: {
    timer () {
      setTimeout(() => {
        if (this.min === 0 && this.sec === '00') {
          this.$emit('done')
        } else {
          if (this.sec === '00') {
            this.min = this.min - 1
            this.sec = '59'
          } else if (+this.sec <= 10) {
            this.sec = '0' + (+this.sec - 1)
          } else {
            this.sec = (+this.sec - 1) + ''
          }
          this.timer()
        }
      }, 1000)
    }
  }
}
</script>

<style scoped>

</style>
