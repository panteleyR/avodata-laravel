export default ({ store }, inject) => {
  inject('notify', {
    show (text, color = 'primary') {
      store.commit('notification/showNotify', { text, color })
    }
  })
}
