export const state = () => ({
  color: 'success',
  show: false,
  text: ''
})

export const getters = {
  show: (state) => {
    return state.show
  },

  text: (state) => {
    return state.text
  },

  color: (state) => {
    return state.color
  }
}

export const mutations = {
  clearNotify (state, payload) {
    state.color = 'success'
    state.show = false
    state.text = ''
  },

  showNotify (state, payload) {
    state.text = payload.text
    state.color = payload.color ? payload.color : 'success'
    state.show = true
  }
}
