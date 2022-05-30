export default function ({ app, $axios, $cookies, $routeri18n, $notify }, inject) {
  if (app.$cookies.get('access-token')) {
    $axios.defaults.headers.common.Authorization = 'Bearer ' + app.$cookies.get('access-token')
  }

  $axios.interceptors.request.use((config) => {
    const hostUrl = process.env.dev ? 'http://web' : process.env.baseUrl

    /**
     * @TODO Need minified this conditions
     */
    if (config.url.startsWith('api')) {
      if (process.server) {
        config.baseURL = hostUrl + (config.url.startsWith('api') ? '' : '/v' + process.env.apiVersion)
      } else {
        config.baseURL = process.env.baseUrl + (config.url.startsWith('api') ? '' : '/v' + process.env.apiVersion)
      }
    } else if (config.url.startsWith('px')) {
      if (process.server) {
        config.baseURL = hostUrl + (config.url.startsWith('px') ? '' : '/v' + process.env.apiVersion)
      } else {
        config.baseURL = process.env.baseUrl + (config.url.startsWith('px') ? '' : '/v' + process.env.apiVersion)
      }
    } else if (process.server) {
      config.baseURL = `${hostUrl}/api/v${process.env.apiVersion}`
    } else {
      config.baseURL = `${process.env.baseUrl}api/v${process.env.apiVersion}`
    }
    return config
  })

  if (process.client) {
    $axios.interceptors.response.use((response) => {
      return response
    }, (error) => {
      if (error.response.status === 401) {
        app.$notify.show('Вы не авторизованы', 'error')
        app.$routeri18n.push('login')
      }

      if (error.response.status === 403) {
        app.$notify.show('У вас недостаточно прав', 'error')
      }

      if (error.response.data.appCode === 500) {
        app.$notify.show('Ошибка сервера', 'error')
      }

      if (error.response.data.appCode === 504) {
        app.$notify.show('Время ожидание от сервера закончилось', 'error')
      }

      if (error.response.data.appCode === 400) {
        app.$notify.show('Ошибка запроса', 'error')
      }

      if (error.response.data.appCode === 422) {
        app.$notify.show('Неверные данные', 'error')
      }

      return Promise.reject(error)
    })
  }
}
