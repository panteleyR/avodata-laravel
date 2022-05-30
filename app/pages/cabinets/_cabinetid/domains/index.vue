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
                src="https://images.unsplash.com/photo-1427751840561-9852520f8ce8?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1510&q=80"
                height="200px"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                class="special-card-page rounded-t"
              >
                <v-row align="end" class="lightbox white--text px-2 fill-height">
                  <v-col>
                    <v-btn
                      v-if="!$auth.isAnalytic()"
                      color="primary"
                      dark
                      @click="createDomain"
                    >
                      Добавить домен
                    </v-btn>
                  </v-col>
                </v-row>
              </v-img>
            </v-col>
          </template>
          <v-card-text class="px-5 py-3">
            <v-data-table
              :headers="headers"
              :items="listDomains"
              :loading="loadingTable"
            >
              <template v-slot:item.delete="{ item }">
                <v-btn icon>
                  <v-icon
                    v-if="!$auth.isAnalytic()"
                    small
                    color="error"
                    @click="deleteDomain(item.id)"
                  >
                    mdi-delete
                  </v-icon>
                </v-btn>
              </template>
              <template v-slot:item.edit="{ item }">
                <v-btn icon>
                  <v-icon
                    v-if="!$auth.isAnalytic()"
                    small
                    @click="$routeri18n.push({ name: 'cabinets-cabinetid-domains-id', params: { cabinetid: $route.params.cabinetid, id: item.id } })"
                  >
                    mdi-pencil
                  </v-icon>
                </v-btn>
              </template>
              <template v-slot:item.tariff="{ item }">
                <v-col
                  v-if="item.current_campaign !== null"
                  cols="12"
                  align-self="center"
                >
                  {{ $store.getters['tariff/getById'](item.current_campaign.tariff_id) ? $store.getters['tariff/getById'](item.current_campaign.tariff_id).name : '' }}
                </v-col>
                <v-col
                  v-else
                  cols="12"
                  align-self="center"
                >
                  Нет текущей кампании
                </v-col>
              </template>
              <template v-slot:item.balance="{ item }">
                <v-col
                  v-if="item.current_campaign !== null"
                  cols="12"
                  align-self="center"
                >
                  {{ item.current_campaign.balance }}
                </v-col>
                <v-col
                  v-else
                  cols="12"
                  align-self="center"
                >
                  0
                </v-col>
              </template>
              <template v-slot:item.on="{ item }">
                <v-switch
                  :input-value="item.current_campaign === null ? false : item.on"
                  :disabled="item.current_campaign === null"
                  :readonly="$auth.isAnalytic() || loadingDomainOn"
                  class="ma-2"
                  label="On"
                  @change="toggleDomain(item)"
                />
              </template>
              <template v-slot:item.geo_on="{ item }">
                <v-switch :input-value="item.geo_on" :readonly="$auth.isAnalytic() || loadingDomainGeoOn" class="ma-2" label="Гео-фильтр" @change="toggleGeoDomain(item)" />
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
            {{ $t('domains.dialog.create.header') }}

            <v-spacer />

            <v-icon
              aria-label="Close"
              @click="dialogCreate = false"
            >
              mdi-close
            </v-icon>
          </v-card-title>

          <v-form @submit.stop.prevent="tryCreateDomain">
            <v-container class="py-0">
              <v-row>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-alpha-n-box</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-text-field
                    ref="name"
                    v-model="name"
                    :label="$t('name')"
                    class="purple-input"
                    :error-messages="nameErrors"
                    @input="$v.name.$touch()"
                    @blur="$v.name.$touch()"
                  />
                </v-col>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-web</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-text-field
                    v-model="domain"
                    :label="$t('domain')"
                    class="purple-input"
                    :error-messages="domainErrors"
                    @input="$v.domain.$touch()"
                    @blur="$v.domain.$touch()"
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
                    :disabled="this.$v.name.$invalid || this.$v.domain.$invalid"
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
        v-model="dialogDelete"
        light
        max-width="500"
      >
        <v-card class="text-center">
          <v-card-title>
            {{ $t('domains.dialog.delete.header') }}

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
              {{ $t('domains.dialog.delete.description') }}
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
              @click="tryDeleteDomain"
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
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'Domains',

  async fetch () {
    await this.$store.dispatch('tariff/getAll')
    await this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid })
  },

  data () {
    return {
      dialogDelete: false,
      deleteDomainId: null,
      loadingDelete: false,
      dialogCreate: false,
      loadingCreate: false,
      loadingTable: false,
      loadingDomainOn: false,
      loadingDomainGeoOn: false,
      domain: '',
      name: '',
      headers: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          text: 'Имя',
          value: 'name'
        },
        {
          sortable: false,
          text: 'Домен',
          value: 'domain'
        },
        {
          sortable: false,
          text: 'On',
          value: 'on',
          align: 'left'
        },
        {
          sortable: false,
          text: 'Тариф',
          value: 'tariff',
          align: 'left'
        },
        {
          sortable: false,
          text: 'Баланс',
          value: 'balance',
          align: 'left'
        },
        {
          sortable: false,
          text: 'Гео-фильтр',
          value: 'geo_on',
          align: 'left'
        },
        {
          text: 'Удалить',
          value: 'delete',
          sortable: false,
          align: 'center'
        },
        {
          text: 'Редактировать',
          value: 'edit',
          sortable: false,
          align: 'center'
        }
      ]
    }
  },

  computed: {
    domainErrors () {
      const errors = []
      if (!this.$v.domain.$dirty) {
        return errors
      }

      !this.$v.domain.required && errors.push(this.$t('auth.validate.require'))
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

    listDomains () {
      return this.$store.getters['domain/list']
    }
  },

  validations: {
    domain: {
      required
    },
    name: {
      required
    }
  },

  methods: {
    deleteDomain (id) {
      this.dialogDelete = true
      this.deleteDomainId = id
    },

    dataClear () {
      this.domain = ''
      this.name = ''
    },

    createDomain () {
      this.dialogCreate = true
    },

    toggleDomain (item) {
      this.loadingDomainOn = true
      this.$store.dispatch('domain/update', {
        cabinetid: this.$route.params.cabinetid,
        id: item.id,
        data: {
          name: item.name,
          domain: item.domain,
          jsBefore: item.js_before,
          jsAfter: item.js_after,
          api_url: item.api_url,
          token: item.token,
          geo_on: item.geo_on,
          on: !item.on
        }
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
          this.loadingDomainOn = false
        })
        this.$notify.show(this.$t('domains.notify.update.success'), 'success')
      }).catch(() => {
        this.loadingDomainOn = false
        this.$notify.show(this.$t('domains.notify.update.error'), 'error')
      })
    },

    toggleGeoDomain (item) {
      this.loadingDomainGeoOn = true
      this.$store.dispatch('domain/update', {
        cabinetid: this.$route.params.cabinetid,
        id: item.id,
        data: {
          name: item.name,
          domain: item.domain,
          jsBefore: item.js_before,
          jsAfter: item.js_after,
          api_url: item.api_url,
          token: item.token,
          geo_on: !item.geo_on,
          on: item.on
        }
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
          this.loadingDomainGeoOn = false
        })
        this.$notify.show(this.$t('domains.notify.update.success'), 'success')
      }).catch(() => {
        this.loadingDomainGeoOn = false
        this.$notify.show(this.$t('domains.notify.update.error'), 'error')
      })
    },

    tryCreateDomain () {
      this.loadingCreate = true
      this.$store.dispatch('domain/create', {
        cabinetid: this.$route.params.cabinetid,
        data: {
          name: this.name,
          domain: this.domain
        }
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
        })
        this.$notify.show(this.$t('domains.notify.create.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('domains.notify.create.error'), 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingCreate = false
        this.dialogCreate = false
      })
    },

    tryDeleteDomain () {
      this.loadingDelete = true
      this.$store.dispatch('domain/delete', { cabinetid: this.$route.params.cabinetid, id: this.deleteDomainId }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid }).finally(() => {
          this.loadingTable = false
        })
        this.$notify.show(this.$t('domains.notify.delete.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('domains.notify.delete.error'), 'error')
      }).finally(() => {
        this.loadingDelete = false
        this.dialogDelete = false
      })
    }
  }
}
</script>

<style scoped>

</style>
