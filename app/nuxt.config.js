// import colors from 'vuetify/es5/util/colors'

export default {
  /*
  ** Nuxt rendering mode
  ** See https://nuxtjs.org/api/configuration-mode
  */
  mode: 'universal',
  /*
  ** Nuxt target
  ** See https://nuxtjs.org/api/configuration-target
  */
  target: 'server',

  env: {
    baseUrl: (process.env.NODE_ENV === 'production') ? (process.env.BASE_URL || 'https://avodata.io/') : 'http://pixel.istream.local/',
    apiVersion: process.env.API_VERSION || 1,
    dev: (process.env.NODE_ENV !== 'production')
  },

  /*
  ** Headers of the page
  ** See https://nuxtjs.org/api/configuration-head
  */
  head: {
    titleTemplate: '%s',
    title: process.env.npm_package_name,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },
  /*
  ** Global CSS
  */
  css: [
    'normalize.css',
    // { src: '~/assets/variables.scss', lang: 'scss'},
    { src: '~/assets/materialdesignicons.min.css', lang: 'css' },
    { src: '~/assets/overrides.sass', lang: 'sass' }
  ],

  styleResources: {
    scss: ['~/assets/variables.scss'], // alternative: scss
    // sass: ['~/assets/overrides.sass'], // alternative: scss
    less: [],
    stylus: []
  },
  /*
  ** Plugins to load before mounting the App
  ** https://nuxtjs.org/guide/plugins
  */
  plugins: [
    '~/plugins/vuelidate',
    '~/plugins/axios',
    '~/plugins/notification',
    '~/plugins/routeri18n',
    '~/plugins/auth'
  ],
  /*
  ** Auto import components
  ** See https://nuxtjs.org/api/configuration-components
  */
  components: [
    '~/components',
    { path: '~/components/core/', prefix: 'core' },
    { path: '~/components/base/', prefix: 'base' }
  ],

  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
    // Doc: https://github.com/nuxt-community/eslint-module
    '@nuxtjs/eslint-module',
    '@nuxtjs/dotenv',
    '@nuxtjs/vuetify'
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    '@nuxtjs/style-resources',
    'nuxt-i18n',
    'cookie-universal-nuxt'
  ],

  i18n: {
    locales: ['ru', 'en'],
    defaultLocale: 'ru',
    vueI18n: {
      fallbackLocale: 'ru',
      messages: {
        en: require('./lang/en.json'),
        ru: require('./lang/ru.json')
      }
    }
  },

  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {},
  /*
  ** vuetify module configuration
  ** https://github.com/nuxt-community/vuetify-module
  */
  vuetify: {
    treeShake: true,
    customVariables: ['~/assets/variables.scss'],
    options: {
      customProperties: true
    },

    theme: {
      // light: true,  //you don't actually need this line as it's for default
      themes: {
        light: {
          primary: '#075194',
          secondary: '#439dd7',
          accent: '#000000',
          error: '#ff1744'
        }
      }
    }
  },

  loading: false,

  /*
  ** Build configuration
  ** See https://nuxtjs.org/api/configuration-build/
  */
  build: {
  }
}
