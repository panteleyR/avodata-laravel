export const state = () => ({
  list: null
})

export const getters = {
  list: (state) => {
    return state.list
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  }
}

export const actions = {
  getAll ({ commit }) {
    return this.$axios.$get('/geo/cities').then((res) => {
      commit('setList', res)
    })
  },
  getSearch ({ commit }, payload) {
    return this.$axios.$get('/geo/cities?search=' + payload).then((res) => {
      commit('setList', res)
    })
  }
}
