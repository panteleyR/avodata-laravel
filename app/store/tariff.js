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
  },
  setRootList (state, payload) {
    state.rootList = payload
  }
}

export const actions = {
  getAll ({ commit }) {
    return this.$axios.$get('/tariffs').then((res) => {
      commit('setList', res)
    })
  },

  update ({ commit }, { id, name, price, contacts, code }) {
    return this.$axios.$put('/tariffs/' + id, {
      name,
      price,
      contacts,
      code
    })
  }
}
