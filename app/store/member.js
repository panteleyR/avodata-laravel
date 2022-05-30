export const state = () => ({
  list: [],
  currentMember: null
})

export const getters = {
  list: (state) => {
    return state.list
  },

  currentMember: (state) => {
    return state.currentMember
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  },

  setCurrentMember (state, payload) {
    state.currentMember = payload
  }
}

export const actions = {
  getAll ({ commit }, { cabinetid }) {
    return this.$axios.$get('/cabinets/' + cabinetid + '/members').then((res) => {
      commit('setList', res)
    })
  },

  getCurrentMember ({ commit }, { cabinetid }) {
    return this.$axios.$get('/cabinets/' + cabinetid + '/members/me')
  },

  create ({ commit, dispatch }, { cabinetid, data }) {
    return this.$axios.$post('/cabinets/' + cabinetid + '/members', data)
  },

  delete ({ commit, dispatch }, { cabinetid, id }) {
    return this.$axios.$delete('/cabinets/' + cabinetid + '/members/' + id)
  },

  update ({ commit, dispatch }, update) {
    return this.$axios.$put('/cabinets/' + update.cabinetid + '/members/' + update.id, update.data)
  },

  show ({ commit, dispatch }, { cabinetid, id }) {
    return this.$axios.$get('/cabinets/' + cabinetid + '/members/' + id)
  }
}
