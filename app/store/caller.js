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
    return this.$axios.$get('/caller').then((res) => {
      commit('setList', res)
    })
  },
  store ({ commit }, { date, phones }) {
    return this.$axios.$post('/caller', {
      date,
      phones
    })
  }
}
