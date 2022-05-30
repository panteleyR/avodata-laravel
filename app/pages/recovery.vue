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
            <v-card-title class="display-2 font-weight-light word-break">
              {{ $t('auth.password-recovery') }}
            </v-card-title>
          </v-col>
        </template>

        <v-form @submit.stop.prevent="tryRestore">
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
                  :label="$t('auth.new-password')"
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
                  :loading="loadingForm"
                  :disabled="this.$v.email.$invalid || this.$v.password.$invalid"
                >
                  {{ $t('auth.recovery.reset-password') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </BaseMaterialCard>
    </v-col>

    <v-dialog
      v-model="dialog"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          {{ $t('auth.well-done') }}

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialog = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text class="pb-0">
          <v-col cols="12" align-self="center">
            {{ $t('auth.verify-description') }}
          </v-col>
          <v-row
            v-if="!refreshConfitmButoon"
            justify="space-between"
            align="center"
          >
            <v-col cols="6" class="py-0">
              <v-text-field
                v-model="code"
                :label="$t('code')"
                class="purple-input"
                :error-messages="codeErrors"
                :disabled="loadingVerify"
                type="number"
                @input="$v.code.$touch(); tryVerify()"
                @blur="$v.code.$touch()"
              />
            </v-col>
            <v-col cols="3" class="py-0">
              <timer v-if="dialog && !refreshConfitmButoon" :min-prop="minute" :sec-prop="second" @done="openRefreshConfirmButton" />
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-actions>
          <v-spacer />

          <v-btn
            v-if="refreshConfitmButoon"
            color="success"
            text
            :loading="loadingVerifyAgain"
            @click="tryAgainVerify"
          >
            {{ $t('auth.send-code-again') }}
          </v-btn>
          <v-btn
            v-if="!refreshConfitmButoon"
            color="success"
            text
            type="submit"
            :disabled="this.$v.code.$invalid"
            :loading="loadingVerify"
            @click="tryVerify"
          >
            {{ $t('auth.send-code') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import { required, minLength, maxLength, email } from 'vuelidate/lib/validators'

export default {
  name: 'Recovery',
  layout: 'auth',

  data () {
    return {
      email: '',
      password: '',
      code: '',
      dialog: false,
      userId: null,
      loadingVerify: false,
      loadingForm: false,
      loadingVerifyAgain: false,
      minute: 5,
      second: '00',
      refreshConfitmButoon: false,
      show1: false,
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
    },
    codeErrors () {
      const errors = []
      if (!this.$v.code.$dirty) {
        return errors
      }

      !this.$v.code.required && errors.push(this.$t('auth.validate.require'))
      !this.$v.code.maxLength && errors.push(this.$t('auth.validate.code.length'))
      !this.$v.code.minLength && errors.push(this.$t('auth.validate.code.length'))
      return errors
    }
  },

  methods: {
    tryRestore () {
      this.loadingForm = true
      this.$axios.$post('/restore', {
        email: this.email,
        password: this.password
      }).then((response) => {
        this.userId = response.userId
        this.dialog = true
      }).catch((error) => {
        if (error.response.status === 422) {
          if (Object.prototype.hasOwnProperty.call(error.response.data.errors, 'email')) {
            this.$notify.show(this.$t('auth.notify.email-doesnt-exist'), 'error')
          }
        }
      }).finally(() => {
        this.loadingForm = false
      })
    },

    tryAgainVerify () {
      this.loadingVerifyAgain = true
      this.$axios.$post('/confirm/auth/restore-again', {
        userId: this.userId
      }).then((response) => {
        this.refreshConfitmButoon = false
      }).finally(() => {
        this.loadingVerifyAgain = false
      })
    },

    tryVerify () {
      if (this.code.length !== 6) {
        return
      }

      this.loadingVerify = true
      this.$axios.$post('/confirm/auth/restore', {
        code: this.code,
        password: this.password,
        userId: this.userId
      }).then((response) => {
        this.dialog = false
        this.$router.replace('/login')
        this.$notify.show(this.$t('auth.recovery.notify.success-change-password'), 'success')
      }).catch((error) => {
        if (error.response.data.appCode === 4001) {
          this.$notify.show(this.$t('auth.notify.invalid-code'), 'error')
        }
      }).finally(() => {
        this.loadingVerify = false
      })
    },

    openRefreshConfirmButton () {
      this.refreshConfitmButoon = true
    }
  },

  validations: {
    email: {
      required,
      email,
      minLength: minLength(8)
    },
    code: {
      required,
      minLength: minLength(6),
      maxLength: maxLength(6)
    },
    password: {
      required,
      minLength: minLength(8)
    }
  },

  mounted () {
    this.$store.commit('setPageName', 'Сбросить пароль')
    this.$refs.email.focus()
  }
}
</script>

<style scoped>
.word-break{
  word-break: break-word;
}
</style>
