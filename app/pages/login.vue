<template>
  <v-row align="center" justify="center">
    <v-col md="4" sm="6">
      <BaseMaterialCard
        color="warning"
        class="px-5 py-3"
        light
      >
        <template v-slot:customHeading>
          <v-col class="pa-0" cols="12">
            <v-card-title class="display-2 font-weight-light">
              {{ $t('auth.login') }}
            </v-card-title>
          </v-col>
        </template>

        <v-form @submit.stop.prevent="tryLogin()">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-email</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="email"
                  v-model="email"
                  :label="$t('email')"
                  class="purple-input"
                  :error-messages="emailErrors"
                  @input="$v.email.$touch()"
                  @blur="$v.email.$touch()"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-account-key</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="password"
                  :label="$t('password')"
                  class="purple-input"
                  :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                  :rules="[rules.required, rules.min]"
                  :type="show1 ? 'text' : 'password'"
                  :error-messages="passwordErrors"
                  @click:append="show1 = !show1"
                  @input="$v.password.$touch()"
                  @blur="$v.password.$touch()"
                />
              </v-col>

              <v-col
                cols="12"
                class="text-right"
              >
                <v-btn
                  color="primary"
                  class="mr-0"
                  type="submit"
                  :disabled="this.$v.email.$invalid || this.$v.password.$invalid"
                  :loading="loading"
                >
                  {{ $t('auth.login') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </BaseMaterialCard>
    </v-col>
  </v-row>
</template>

<script>
import { required, minLength, email } from 'vuelidate/lib/validators'

export default {
  name: 'Login',
  layout: 'auth',

  data () {
    return {
      show1: false,
      email: '',
      password: '',
      loading: false,
      rules: {
        required: value => !!value || 'Required.'
      }
    }
  },

  computed: {
    emailErrors () {
      const errors = []
      if (!this.$v.email.$dirty) {
        return errors
      }

      !this.$v.email.email && errors.push(this.$t('auth.validate.email'))
      !this.$v.email.required && errors.push(this.$t('auth.validate.require'))
      !this.$v.email.minLength && errors.push(this.$t('auth.validate.email.min-length'))
      return errors
    },
    passwordErrors () {
      const errors = []
      if (!this.$v.password.$dirty) {
        return errors
      }

      !this.$v.password.required && errors.push(this.$t('auth.validate.require'))
      !this.$v.password.minLength && errors.push(this.$t('auth.validate.password.min-length'))
      return errors
    }
  },

  methods: {
    tryLogin () {
      this.loading = true
      this.$axios.$post('/login', {
        email: this.email,
        password: this.password
      }).then((response) => {
        // window.localStorage.setItem('access-token', response.access_token)
        this.$cookies.set('access-token', response.access_token)
        this.$axios.defaults.headers.common.Authorization = 'Bearer ' + this.$cookies.get('access-token')
        this.$routeri18n.push('cabinets')
      }).catch((error) => {
        if (error.response.data.appCode === 4001) {
          this.$notify.show(this.$t('auth.notify.invalid-user'), 'error')
        }

        if (error.response.status === 422) {
          for (const key in error.response.data.errors) {
            this.$notify.show(error.response.data.errors[key][0], 'error')
            break
          }
        }
      }).finally(() => {
        this.loading = false
      })
    }
  },

  validations: {
    email: {
      required,
      email,
      minLength: minLength(8)
    },
    password: {
      required,
      minLength: minLength(8)
    }
  },

  mounted () {
    this.$refs.email.focus()
  }
}
</script>

<style scoped>

</style>
