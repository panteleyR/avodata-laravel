<template>
  <v-container>
    <v-row>
      <v-col
        cols="12"
      >
        <BaseMaterialCard
          color="warning"
          class="px-0 py-0 my-0"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-img
                src="https://images.unsplash.com/photo-1448697138198-9aa6d0d84bf4?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80"
                height="200px"
                class="special-card-page rounded-t"
              />
              <v-card-title class="pb-0 display-2 font-weight-light">
                {{ $store.getters['user/currentUser'].name }}
              </v-card-title>
            </v-col>
          </template>

          <v-card-text class="px-5 py-3">
            <div class="display-1 mb-1">
              <span class="font-weight-bold">Email:</span> {{ $store.getters['user/currentUser'].email }}
            </div>
            <div class="display-1 mb-1">
              <span class="font-weight-bold">Дата регистрации:</span> {{ $store.getters['user/currentUser'].email_verified_at }}
            </div>
            <div class="display-1 mb-1">
              <span class="font-weight-bold">Роль:</span> {{ $auth.getRoleName() }}
            </div>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              text
              type="submit"
              @click="dialogChange = true"
            >
              Сменить пароль
            </v-btn>
          </v-card-actions>
        </BaseMaterialCard>
      </v-col>
    </v-row>

    <v-dialog
      v-model="dialogChange"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Сменить пароль

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogChange = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-row
            justify="space-between"
            align="center"
          />
        </v-card-text>
        <v-form @submit.stop.prevent="tryChangePassword">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-key</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="passwordOld"
                  :label="'Старый пароль'"
                  class="purple-input"
                  type="password"
                  :error-messages="passwordErrors"
                  @input="$v.passwordOld.$touch()"
                  @blur="$v.passwordOld.$touch()"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-key-plus</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="passwordNew"
                  :label="'Новый пароль'"
                  class="purple-input"
                  type="password"
                  :error-messages="passwordNewErrors"
                  @input="$v.passwordNew.$touch()"
                  @blur="$v.passwordNew.$touch()"
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
                  :loading="loadingChange"
                  :disabled="this.$v.passwordOld.$invalid || $v.passwordNew.$invalid"
                >
                  Сменить
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'

export default {
  name: 'Profile',

  data () {
    return {
      dialogChange: false,
      loadingChange: false,
      passwordOld: '',
      passwordNew: ''
    }
  },

  computed: {
    passwordErrors () {
      const errors = []
      if (!this.$v.passwordOld.$dirty) {
        return errors
      }

      !this.$v.passwordOld.required && errors.push(this.$t('auth.validate.require'))
      !this.$v.passwordOld.minLength && errors.push(this.$t('auth.validate.password.min-length'))

      return errors
    },
    passwordNewErrors () {
      const errors = []
      if (!this.$v.passwordNew.$dirty) {
        return errors
      }

      !this.$v.passwordNew.required && errors.push(this.$t('auth.validate.require'))
      !this.$v.passwordNew.minLength && errors.push(this.$t('auth.validate.password.min-length'))

      return errors
    }
  },

  validations: {
    passwordOld: {
      required,
      minLength: minLength(8)
    },
    passwordNew: {
      required,
      minLength: minLength(8)
    }
  },

  methods: {
    tryChangePassword () {
      this.loadingChange = true
      this.$axios.$post('/users/me/change-password', {
        passwordOld: this.passwordOld,
        passwordNew: this.passwordNew
      }).then(() => {
        this.$notify.show(this.$t('users.notify.update.success'), 'success')
      }).catch((error) => {
        if (error.response.status === 403) {
          this.$notify.show('Неверный пароль', 'error')
        }

        // if (error.response.data.appCode === 4001) {
        //   this.$notify.show('Неверный пароль', 'error')
        // }
      }).finally(() => {
        this.loadingChange = false
        this.dialogChange = false
        this.passwordOld = ''
        this.passwordNew = ''
      })
    }
  }
}
</script>

<style scoped>
</style>
