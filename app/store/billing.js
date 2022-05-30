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
  getAll ({ commit }, { cabinetId, domainId, campaignId, from, to }) {
    const params = {}
    if (cabinetId !== undefined) {
      params.cabinetId = cabinetId
    }
    if (domainId !== undefined) {
      params.domainId = domainId
    }
    if (campaignId !== undefined) {
      params.campaignId = campaignId
    }
    if (from !== undefined) {
      params.from = from
    }
    if (to !== undefined) {
      params.to = to
    }
    return this.$axios.$get('/payments', {
      params
    }).then((res) => {
      commit('setList', res)
    })
  },
  delete ({ commit }, { cabinetId, domainId, campaignId }) {
    return this.$axios.$delete('/cabinets/' + cabinetId + '/domains/' + domainId + '/campaigns/' + campaignId)
  },
  update ({ commit }, { cabinetId, domainId, campaignId, tariffId, balance, discount, from, to }) {
    return this.$axios.$put('/cabinets/' + cabinetId + '/domains/' + domainId + '/campaigns/' + campaignId, {
      tariffId,
      balance,
      discount,
      from,
      to
    })
  },
  store ({ commit }, { cabinetId, domainId, campaignId, arrival, date }) {
    return this.$axios.$post('/cabinets/' + cabinetId + '/domains/' + domainId + '/campaigns/' + campaignId + '/payments', {
      arrival,
      date
    })
  }
}
