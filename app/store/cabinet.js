export const state = () => ({
  list: [],
  currentCabinet: null
})

export const getters = {
  list: (state) => {
    return state.list
  },

  currentCabinet: (state) => {
    return state.currentCabinet
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  },

  setCurrentCabinet (state, payload) {
    state.currentCabinet = payload
  }
}

export const actions = {
  getAll ({ commit }) {
    return this.$axios.$get('/cabinets').then((res) => {
      commit('setList', res)
    })
  },

  getCurrentCabinet ({ commit }, { cabinetid }) {
    return this.$axios.$get('/cabinets/' + cabinetid)
  },

  create ({ commit, dispatch }, data) {
    return this.$axios.$post('/cabinets', data)
  },

  delete ({ commit, dispatch }, id) {
    return this.$axios.$delete('/cabinets/' + id)
  },

  show ({ commit, dispatch }, id) {
    return this.$axios.$get('/cabinets/' + id)
  },

  update ({ commit, dispatch }, update) {
    return this.$axios.$put('/cabinets/' + update.id, update.data)
  },

  attach ({ commit, dispatch }, { id, providerId }) {
    return this.$axios.$patch('/cabinets/' + id + '/attach/provider/' + providerId)
  },
  detach ({ commit, dispatch }, { id, providerId }) {
    return this.$axios.$patch('/cabinets/' + id + '/detach/provider/' + providerId)
  }
}
