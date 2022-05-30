module.exports = {
  apps: [
    {
      name: 'NuxtAppName',
      exec_mode: 'cluster',
      instances: 'max', // Or a number of instances
      script: './node_modules/nuxt/bin/nuxt.js',
      env: {
        PORT: '3001'
      },
      args: 'start'
    },
    {
      name: 'NuxtAppStageName',
      exec_mode: 'cluster',
      instances: 'max', // Or a number of instances
      script: './node_modules/nuxt/bin/nuxt.js',
      env: {
        PORT: '3011'
      },
      args: 'start'
    }
  ]
}
