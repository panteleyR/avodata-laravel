export const state = () => ({
  list: [],
  rootList: []
})

export const getters = {
  list: (state) => {
    return state.list
  },
  rootList: (state) => {
    return state.rootList
  },
  getById: (state) => {
    return (id) => {
      return state.list.find((val) => {
        return val.id === id
      })
    }
  },
  getRootById: (state) => {
    return (id) => {
      return state.rootList.find((val) => {
        return val.id === id
      })
    }
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  },
  setRootList (state, payload) {
    state.rootList = payload
  }
}

export const actions = {
  getAll ({ commit }, { cabinetid }) {
    return this.$axios.$get('/cabinets/' + cabinetid + '/domains').then((res) => {
      commit('setList', res)
    })
  },

  rootGetAll ({ commit }) {
    return this.$axios.$get('/domains').then((res) => {
      commit('setRootList', res)
    })
  },

  create ({ commit, dispatch }, { cabinetid, data }) {
    return this.$axios.$post('/cabinets/' + cabinetid + '/domains', data)
  },

  delete ({ commit, dispatch }, { cabinetid, id }) {
    return this.$axios.$delete('/cabinets/' + cabinetid + '/domains/' + id)
  },

  update ({ commit, dispatch }, update) {
    return this.$axios.$put('/cabinets/' + update.cabinetid + '/domains/' + update.id, update.data)
  },

  show ({ commit, dispatch }, { cabinetid, id }) {
    return this.$axios.$get('/cabinets/' + cabinetid + '/domains/' + id)
  }
}
