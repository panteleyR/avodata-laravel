export const state = () => ({
  list: []
})

export const getters = {
  list: (state) => {
    return state.list
  },
  getById: (state) => {
    return (id) => {
      return state.list.find((val) => {
        return val.id === id
      })
    }
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  }
}

export const actions = {
  getAll ({ commit }) {
    return this.$axios.$get('/providers').then((res) => {
      commit('setList', res)
    })
  },

  create ({ commit }, data) {
    return this.$axios.$post('/providers', data)
  },

  update ({ commit }, data) {
    return this.$axios.$put('/providers/' + data.id, data)
  },

  delete ({ commit }, id) {
    return this.$axios.$delete('/providers/' + id)
  }
}
