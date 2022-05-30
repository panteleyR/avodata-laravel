export const state = () => ({
  currentUser: null,
  listUsers: []
})

export const getters = {
  currentUser: (state) => {
    return state.currentUser
  },

  listUsers: (state) => {
    return state.listUsers
  }
}

export const mutations = {
  setCurrentUser (state, payload) {
    state.currentUser = payload
  },

  setListUsers (state, payload) {
    state.listUsers = payload
  }
}

export const actions = {
  getCurrentUser ({ commit }) {
    return this.$axios.$get('/users/me')
  },

  getAll ({ commit }) {
    return this.$axios.$get('/users').then((res) => {
      commit('setListUsers', res)
    })
  },

  create ({ commit, dispatch }, data) {
    return this.$axios.$post('/users', data)
  },

  update ({ commit, dispatch }, update) {
    return this.$axios.$put('/users/' + update.id, update.data)
  },

  delete ({ commit, dispatch }, id) {
    return this.$axios.$delete('/users/' + id)
  }
}
