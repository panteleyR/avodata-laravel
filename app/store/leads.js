export const state = () => ({
  list: [],
  rootList: [],
  sessions: [],
  statistics: null,
  rootStatistics: null,
  rootProviders: { providers: [], contacts: [] },
  rootUserSessions: [],
  userSessions: [],
  rootTotalPage: null,
  rootLastPage: null,
  rootLastPageSessions: null,
  rootTotalPageSessions: null,
  totalPage: null,
  lastPage: null,
  lastPageSessions: null,
  totalPageSessions: null
})

export const getters = {
  list: (state) => {
    return state.list
  },

  rootList: (state) => {
    return state.rootList
  },

  sessions: (state) => {
    return state.sessions
  },

  userSessions: (state) => {
    return state.userSessions
  },

  statistics: (state) => {
    return state.statistics
  },

  rootStatistics: (state) => {
    return state.rootStatistics
  },

  rootProviders: (state) => {
    return state.rootProviders
  },

  rootUserSessions: (state) => {
    return state.rootUserSessions
  },

  getRootProvidersById: (state) => {
    return (id) => {
      return state.rootProviders.providers.find((val) => {
        return val.provider_id === id
      })
    }
  },

  getRootContactsById: (state) => {
    return (id) => {
      return state.rootProviders.contacts.find((val) => {
        return val.provider_id === id
      })
    }
  },

  sessionsMonthStatisticsLabel: (state) => {
    return state.statistics ? Object.keys(state.statistics.sessionsMonthStatistics) : []
  },

  sessionsMonthStatisticsData: (state) => {
    return state.statistics ? Object.values(state.statistics.sessionsMonthStatistics) : []
  },

  usersMonthStatisticsLabel: (state) => {
    return state.statistics ? Object.keys(state.statistics.usersMonthStatistics) : []
  },

  usersMonthStatisticsData: (state) => {
    return state.statistics ? Object.values(state.statistics.usersMonthStatistics) : []
  },
  rootTotalPage: (state) => {
    return state.rootTotalPage
  },
  rootLastPage: (state) => {
    return state.rootLastPage
  },
  rootLastPageSessions: (state) => {
    return state.rootLastPageSessions
  },
  rootTotalPageSessions: (state) => {
    return state.rootTotalPageSessions
  },
  totalPage: (state) => {
    return state.totalPage
  },
  lastPage: (state) => {
    return state.lastPage
  },
  lastPageSessions: (state) => {
    return state.lastPageSessions
  },
  totalPageSessions: (state) => {
    return state.totalPageSessions
  }
}

export const mutations = {
  setList (state, payload) {
    state.list = payload
  },

  setSessions (state, payload) {
    state.sessions = payload
  },

  setUserSessions (state, payload) {
    state.userSessions = payload
  },

  setStatistics (state, payload) {
    if (payload.length === 0) {
      state.statistics = null
    } else {
      state.statistics = payload
    }
  },
  setRootStatistics (state, payload) {
    if (payload.length === 0) {
      state.rootStatistics = null
    } else {
      state.rootStatistics = payload
    }
  },
  setRootProviders (state, payload) {
    state.rootProviders = payload
  },
  setRootUsers (state, payload) {
    state.rootList = payload
  },
  setRootUserSessions (state, payload) {
    state.rootUserSessions = payload
  },
  setRootTotalPage (state, payload) {
    state.rootTotalPage = payload
  },
  setRootLastPage (state, payload) {
    state.rootLastPage = payload
  },
  setRootLastPageSessions (state, payload) {
    state.rootLastPageSessions = payload
  },
  setRootTotalPageSessions (state, payload) {
    state.rootTotalPageSessions = payload
  },
  setTotalPage (state, payload) {
    state.totalPage = payload
  },
  setLastPage (state, payload) {
    state.lastPage = payload
  },
  setLastPageSessions (state, payload) {
    state.lastPageSessions = payload
  },
  setTotalPageSessions (state, payload) {
    state.totalPageSessions = payload
  }
}

export const actions = {
  getAll ({ commit }, { cabinetId, data }) {
    return this.$axios
      .$get('px/v1/public/' + cabinetId + '/users', { params: data })
      .then((res) => {
        commit('setLastPage', res.last_page)
        commit('setTotalPage', res.total)
        commit('setList', res.data)
      })
  },

  getSessions ({ commit }, cabinetId) {
    return this.$axios
      .$get('px/v1/public/' + cabinetId + '/sessions')
      .then((data) => {
        commit('setSessions', data)
      })
  },

  getUserSessions ({ commit }, { cabinetId, userId, perPage, page }) {
    return this.$axios
      .$get('px/v1/public/' + cabinetId + '/users/' + userId + '/sessions', { params: { perPage, page } })
      .then((res) => {
        commit('setLastPageSessions', res.last_page)
        commit('setTotalPageSessions', res.total)
        commit('setUserSessions', res.data)
      })
  },

  getStatistics ({ commit }, { cabinetId, data }) {
    return this.$axios
      .$get('px/v1/public/' + cabinetId + '/statistics', { params: data }).then((res) => {
        commit('setStatistics', res)
      })
  },

  getRootStatistics ({ commit }) {
    return this.$axios.$get('px/v1/admin/statistics').then((res) => {
      commit('setRootStatistics', res)
    })
  },

  getRootUsers ({ commit }, data) {
    return this.$axios
      .$get('px/v1/admin/users', { params: data })
      .then((res) => {
        commit('setRootLastPage', res.last_page)
        commit('setRootTotalPage', res.total)
        commit('setRootUsers', res.data)
      })
  },

  // getRootSession () {
  //   return this.$axios.$get('px/v1/admin/users').then((res) => {
  //
  //   })
  // },

  getRootProviders ({ commit }, data) {
    return this.$axios.$get('px/v1/admin/statistics/providers', { params: data }).then((res) => {
      commit('setRootProviders', res)
    })
  },
  getRootUserSessions ({ commit }, { perPage, page, id }) {
    return this.$axios.$get('px/v1/admin/users/' + id + '/sessions', { params: { perPage, page } })
      .then((res) => {
        commit('setRootLastPageSessions', res.last_page)
        commit('setRootTotalPageSessions', res.total)
        commit('setRootUserSessions', res.data)
      })
  }
}
