<template>
  <v-container>
    <v-row>
      <v-col
        cols="12"
      >
        <BaseMaterialCard
          v-if="!listUsers"
          class="px-5 py-3 my-0"
        >
          <template v-slot:customHeading>
            <div>
              К сожалению, у вас пока нет достаточных данных
              <v-icon>mdi-emoticon-sad-outline</v-icon>
            </div>
          </template>
        </BaseMaterialCard>
        <BaseMaterialCard
          v-else
          class="px-5 py-3 my-0"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="pb-0 display-2 font-weight-light">
                <!--                {{ $t('users.header') }}-->
                <!--                <v-spacer></v-spacer>-->
                <v-btn
                  v-if="$auth.isSuperAdmin() || $auth.isAdmin()"
                  color="primary"
                  dark
                  class="mb-2"
                  @click="createUser"
                >
                  Пригласить пользователя
                </v-btn>
              </v-card-title>
            </v-col>
          </template>
          <v-card-text>
            <v-data-table
              :headers="authHeaders"
              :items="listUsers"
              :loading="loadingTable"
            >
              <template v-slot:item.role.name="{ item }">
                <div>
                  {{ $auth.roleNameTransform(item.role.name) }}
                </div>
              </template>
              <template v-slot:item.delete="{ item }">
                <v-btn
                  v-if="item.user.email !== $store.getters['user/currentUser'].email"
                  icon
                >
                  <v-icon
                    small
                    color="error"
                    @click="deleteUser(item)"
                  >
                    mdi-delete
                  </v-icon>
                </v-btn>
              </template>
              <template v-slot:item.edit="{ item }">
                <v-btn
                  v-if="item.user.email !== $store.getters['user/currentUser'].email"
                  icon
                >
                  <v-icon
                    small
                    @click="updateUser(item)"
                  >
                    mdi-pencil
                  </v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </BaseMaterialCard>
      </v-col>

      <v-dialog
        v-model="dialogCreate"
        light
        max-width="500"
      >
        <v-card class="text-center">
          <v-card-title>
            {{ $t('users.dialog.create.header') }}

            <v-spacer />

            <v-icon
              aria-label="Close"
              @click="dialogCreate = false"
            >
              mdi-close
            </v-icon>
          </v-card-title>

          <v-card-text>
            <!--            <v-col cols="12" align-self="center">-->
            <!--              {{ $t('users.dialog.create.description') }}-->
            <!--            </v-col>-->
            <v-row
              justify="space-between"
              align="center"
            />
          </v-card-text>
          <v-form @submit.stop.prevent="tryCreateUser">
            <v-container class="py-0">
              <v-row>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-email</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-text-field
                    v-model="email"
                    :label="$t('email')"
                    class="purple-input"
                    :error-messages="emailErrors"
                    @input="$v.email.$touch()"
                    @blur="$v.email.$touch()"
                  />
                </v-col>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-account-tie</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-select
                    v-model="role"
                    :items="roles"
                    :error-messages="roleErrors"
                    item-text="name"
                    item-value="id"
                    label="Роль"
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
                    :loading="loadingCreate"
                    :disabled="this.$v.email.$invalid || this.$v.role.$invalid"
                  >
                    {{ $t('add') }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </v-card>
      </v-dialog>
      <v-dialog
        v-model="dialogUpdate"
        light
        max-width="500"
      >
        <v-card class="text-center">
          <v-card-title>
            {{ $t('users.dialog.update.header') }}

            <v-spacer />

            <v-icon
              aria-label="Close"
              @click="dialogUpdate = false"
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
          <v-form @submit.stop.prevent="tryUpdateUser">
            <v-container class="py-0">
              <v-row>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-account-tie</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-select
                    v-model="role"
                    :items="roles"
                    :error-messages="roleErrors"
                    item-text="name"
                    item-value="id"
                    label="Роль"
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
                    :loading="loadingUpdate"
                    :disabled="this.$v.role.$invalid"
                  >
                    {{ $t('update') }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </v-card>
      </v-dialog>
      <v-dialog
        v-model="dialogDelete"
        light
        max-width="500"
      >
        <v-card class="text-center">
          <v-card-title>
            {{ $t('users.dialog.delete.header') }}

            <v-spacer />

            <v-icon
              aria-label="Close"
              @click="dialogDelete = false"
            >
              mdi-close
            </v-icon>
          </v-card-title>

          <v-card-text>
            <v-col cols="12" align-self="center">
              {{ $t('users.dialog.delete.description') }}
            </v-col>
            <v-row
              justify="space-between"
              align="center"
            />
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              color="error"
              text
              type="submit"
              :loading="loadingDelete"
              @click="tryDeleteUser"
            >
              {{ $t('delete') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </v-container>
</template>

<script>
import { required, minLength, email } from 'vuelidate/lib/validators'

export default {
  name: 'Users',

  data () {
    return {
      dialogDelete: false,
      deleteUserId: null,
      loadingDelete: false,
      dialogCreate: false,
      loadingCreate: false,
      dialogUpdate: false,
      loadingUpdate: false,
      updateUserId: null,
      loadingTable: false,
      email: '',
      name: '',
      password: '',
      role: null,
      headers: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          text: this.$t('name'),
          value: 'user.name'
        },
        {
          sortable: false,
          text: this.$t('email'),
          value: 'user.email'
        },
        {
          sortable: false,
          text: this.$t('role'),
          value: 'role.name',
          align: 'right'
        },
        {
          text: 'Удалить',
          value: 'delete',
          sortable: false,
          align: 'center'
        },
        {
          text: 'Редактирование',
          value: 'edit',
          sortable: false,
          align: 'center'
        }
      ]
    }
  },

  computed: {
    authHeaders () {
      return this.headers.filter((val) => {
        return !(val.value === 'actions' && (this.$auth.isAnalytic() || this.$auth.isEmployee()))
      })
    },

    roles () {
      return this.$store.getters['role/list'].map((val) => {
        return {
          id: val.id,
          name: this.$auth.roleNameTransform(val.name)
        }
      })
    },

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

    nameErrors () {
      const errors = []
      if (!this.$v.name.$dirty) {
        return errors
      }

      !this.$v.name.required && errors.push(this.$t('auth.validate.require'))
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

    roleErrors () {
      const errors = []
      if (!this.$v.role.$dirty) {
        return errors
      }

      !this.$v.role.required && errors.push(this.$t('auth.validate.require'))
      return errors
    },

    listUsers () {
      return this.$store.getters['member/list']
    }
  },

  validations: {
    email: {
      required,
      email,
      minLength: minLength(8)
    },
    name: {
      required
    },
    password: {
      required,
      minLength: minLength(8)
    },
    role: {
      required
    }
  },

  mounted () {
    this.$store.commit('setPageName', this.$t('users.header'))
  },

  methods: {
    deleteUser (item) {
      this.dialogDelete = true
      this.deleteUserId = item.id
    },

    updateUser (item) {
      this.dialogUpdate = true
      this.updateUserId = item.id
      this.role = item.role.id
    },

    createUser () {
      this.dialogCreate = true
    },

    tryCreateUser () {
      this.loadingCreate = true
      this.$store.dispatch('member/create', {
        data: {
          email: this.email,
          name: this.name,
          password: this.password,
          roleId: this.role
        },
        cabinetid: this.$route.params.cabinetid
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('member/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
        })
        this.$notify.show(this.$t('users.notify.create.success'), 'success')
      }).catch(() => {
        this.$notify.show('Пользователя по такому email не существует в системе', 'error')
      }).finally(() => {
        this.clearData()
        this.loadingCreate = false
        this.dialogCreate = false
      })
    },

    tryDeleteUser () {
      this.loadingDelete = true
      this.$store.dispatch('member/delete', { id: this.deleteUserId, cabinetid: this.$route.params.cabinetid }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('member/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
        })
        this.$notify.show(this.$t('users.notify.delete.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('users.notify.delete.error'), 'error')
      }).finally(() => {
        this.clearData()
        this.loadingDelete = false
        this.dialogDelete = false
      })
    },

    tryUpdateUser () {
      this.loadingUpdate = true
      this.$store.dispatch('member/update', {
        id: this.updateUserId,
        cabinetid: this.$route.params.cabinetid,
        data: {
          roleId: this.role
        }
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('member/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
        })
        this.$notify.show(this.$t('users.notify.update.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('users.notify.update.error'), 'error')
      }).finally(() => {
        this.clearData()
        this.loadingUpdate = false
        this.dialogUpdate = false
      })
    },

    clearData () {
      this.email = ''
      this.name = ''
      this.password = ''
      this.role = null
    }
  },

  async fetch () {
    await this.$store.dispatch('member/getAll', { cabinetid: this.$route.params.cabinetid })
    await this.$store.dispatch('role/getAll')
  }
}
</script>

<style scoped>

</style>
