export default ({ store, app }, inject) => {
  inject('routeri18n', {
    push (route) {
      app.router.push(app.localePath(route))
    }
  })
}
