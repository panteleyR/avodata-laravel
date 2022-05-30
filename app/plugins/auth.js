export default ({ store, app }, inject) => {
  inject('auth', {
    isRole (role) {
      return store.getters['member/currentMember'] ? store.getters['member/currentMember'].role.name === role : false
    },
    isAdmin () {
      return store.getters['member/currentMember'] ? store.getters['member/currentMember'].role.name === 'Admin' : false
    },
    isSuperAdmin () {
      return store.getters['user/currentUser'] ? store.getters['user/currentUser'].is_super_admin : false
    },
    isAnalytic () {
      return store.getters['member/currentMember'] ? store.getters['member/currentMember'].role.name === 'Analytic' : false
    },
    isEmployee () {
      return store.getters['member/currentMember'] ? store.getters['member/currentMember'].role.name === 'Employee' : false
    },
    getRoleName () {
      if (app.$auth.isSuperAdmin()) {
        return 'Master'
      }

      return app.$auth.roleNameTransform(store.getters['member/currentMember'].role.name)
    },
    roleNameTransform (role) {
      if (role === 'Admin') {
        return app.i18n.t('auth.roles.admin.name')
      }

      if (role === 'Employee') {
        return app.i18n.t('auth.roles.employee.name')
      }

      if (role === 'Analytic') {
        return app.i18n.t('auth.roles.analytic.name')
      }

      return role
    }
  })
}
