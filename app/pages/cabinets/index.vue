<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-item-group v-if="!$auth.isSuperAdmin()">
      <v-container>
        <v-row class="black--text">
          <v-col
            v-for="(item, key) in cabinets"
            :key="key"
            cols="12"
            md="4"
          >
            <v-item v-slot:default="{ }">
              <v-card
                :color="'white'"
                class="d-flex align-center"
                height="200"
                light
              >
                <v-btn
                  absolute
                  top
                  right
                  text
                  @click="cabinetUpdate(item)"
                >
                  <v-icon dark color="black">
                    mdi-cog
                  </v-icon>
                </v-btn>
                <div class="cabinet-name display-2 text-center" @click="$routeri18n.push({ name: 'cabinets-cabinetid-dashboard', params: { cabinetid: item.id } })">
                  <div>
                    <div>{{ item.name }}</div>
                    <div class="amount-block">
                      Счет: <span class="amount">{{ item.amount }}</span>
                    </div>
                  </div>
                </div>
              </v-card>
            </v-item>
          </v-col>
          <v-col
            cols="12"
            md="4"
          >
            <v-item v-slot:default="{ }">
              <v-card
                :color="'white'"
                class="d-flex align-center"
                height="200"
                light
                @click="dialogCreate = true"
              >
                <v-btn
                  center
                  fab
                  text
                >
                  <v-icon size="32" dark color="black">
                    mdi-plus
                  </v-icon>
                </v-btn>
                <div>
                  Добавить организацию
                </div>
              </v-card>
            </v-item>
          </v-col>
        </v-row>
      </v-container>
    </v-item-group>

    <BaseMaterialCard
      v-else
      color="warning"
      class="px-5 py-3"
      light
    >
      <template v-slot:customHeading>
        <v-col class="pa-0" cols="12">
          <v-card-title class="display-2 font-weight-light">
            {{ $t('cabinets') }}
            <v-spacer />
            <v-btn text @click="dialogCreate = true">
              <div>Создать кабинет</div>
              <v-icon class="ml-2">
                mdi-plus
              </v-icon>
            </v-btn>
            <v-btn text @click="dialogTariff = true">
              <div>Тарифы</div>
              <v-icon class="ml-2">
                mdi-format-list-bulleted
              </v-icon>
            </v-btn>
          </v-card-title>
        </v-col>
      </template>

      <v-container>
        <v-data-table
          :headers="headers"
          :items="cabinets"
          :loading="loadingTable"
        >
          <template v-slot:item.domains="{ item }">
            <v-container v-if="$vuetify.breakpoint.smAndUp">
              <v-row v-for="(domain, key) in item.domains" :key="key" no-gutters>
                <div class="d-flex align-center">
                  <v-col col="12" class="pa-2">
                    {{ domain.domain }}
                  </v-col>
                  <v-col v-if="domain.script_state === 1 && timeFilter(domain.last_script_activity)" cols="8" class="pa-1">
                    <div class="statusText">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <div class="statusCircle circleGreen" v-on="on" />
                        </template>
                        <span>Скрипт подключен</span>
                      </v-tooltip>
                    </div>
                  </v-col>
                  <v-col v-else-if="(domain.script_state === 0 && timeFilter(domain.last_script_activity)) || (domain.script_state === 1 && !timeFilter(domain.last_script_activity))" cols="8" class="pa-1">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <div class="statusCircle circleOrange" v-on="on" />
                      </template>
                      <span>Последняя активность: {{ dateFormat(domain.last_script_activity) }} </span>
                      <br>
                      <span v-if="domain.script_state">Скрипт подключен</span>
                      <span v-else>Скрипт не подключен</span>
                    </v-tooltip>
                  </v-col>
                  <v-col v-else cols="8" class="pa-1">
                    <div class="statusText">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <div class="statusCircle circleRed" v-on="on" />
                        </template>
                        <span>Скрипт не подключен</span>
                      </v-tooltip>
                    </div>
                  </v-col>
                </div>
                <v-col cols="8" pa-1 align-self="center">
                  <v-switch :readonly="loadingDomainUpdate" :input-value="domain.on" class="ma-2" @change="toggleDomain(domain)" />
                </v-col>
                <v-col
                  v-if="domain.current_campaign !== null"
                  cols="12"
                  align-self="center"
                >
                  <div>Текущая кампания: {{ domain.current_campaign.balance }}</div>
                  <div>От: {{ domain.current_campaign.from }}</div>
                  <div>До: {{ domain.current_campaign.to }}</div>
                  <div>Скидка: {{ domain.current_campaign.discount }} %</div>
                </v-col>
                <v-col
                  v-else
                  cols="12"
                  align-self="center"
                >
                  Нет текущей кампании
                </v-col>
              </v-row>
            </v-container>
            <v-btn
              v-else
              top
              right
              text
              @click="openDialogDomain(item)"
            >
              <v-icon dark>
                mdi-eye
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:item.providers="{ item }">
            <v-container>
              <v-row v-for="(provider, key) in item.providers" :key="key" no-gutters>
                <v-col cols="12" align-self="center">
                  {{ provider.name }}
                </v-col>
              </v-row>
            </v-container>
          </template>
          <template v-slot:item.providersSetting="{ item }">
            <v-btn
              top
              right
              text
              @click="cabinetProviders(item)"
            >
              <v-icon dark>
                mdi-alpha-p-box
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:item.settings="{ item }">
            <v-btn
              top
              right
              text
              @click="cabinetUpdate(item)"
            >
              <v-icon dark>
                mdi-cog
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:item.enter="{ item }">
            <v-btn
              top
              right
              text
              @click="$routeri18n.push({ name: 'cabinets-cabinetid-dashboard', params: { cabinetid: item.id } })"
            >
              <v-icon dark>
                mdi-door
              </v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-container>
    </BaseMaterialCard>

    <v-dialog
      v-model="dialogCreate"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Создание нового кабинета

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogCreate = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryCreateCabinet">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-account</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="createName"
                  v-model="createName"
                  :label="'Имя организации'"
                  class="purple-input"
                  :error-messages="nameErrors"
                  @input="$v.createName.$touch()"
                  @blur="$v.createName.$touch()"
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
                  :disabled="this.$v.createName.$invalid"
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
          Изменение кабинета

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogUpdate = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryUpdateCabinet">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-account</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="updateName"
                  v-model="updateName"
                  :label="'Имя организации'"
                  class="purple-input"
                  :error-messages="nameUpdateErrors"
                  @input="$v.updateName.$touch()"
                  @blur="$v.updateName.$touch()"
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
                  :disabled="this.$v.updateName.$invalid"
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
      v-model="dialogDomain"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Список доменов

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogDomain = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-data-table
            :headers="domainHeaders"
            :items="currentDomains"
          >
            <template v-slot:item.on="{ item }">
              <v-switch :readonly="loadingDomainUpdate" :input-value="item.on" class="ma-2" label="On" @change="toggleDomain(item)" />
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogTariff"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Список тарифов

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogTariff = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-data-table
            :headers="tariffHeaders"
            :items="tariffs"
          >
            <template v-slot:item.edit="{ item }">
              <v-btn
                v-if="item.code !== 'personal'"
                icon
              >
                <v-icon
                  small
                  @click="editTariff(item)"
                >
                  mdi-pencil
                </v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dialogUpdateTariff"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Обновить тариф
          <v-spacer />
          <v-icon
            aria-label="Close"
            @click="dialogUpdateTariff = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryUpdateTariff">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateTariffName"
                  :label="'Имя'"
                  class="purple-input"
                />
              </v-col>              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-currency-usd</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateTariffPrice"
                  :label="'Цена'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-text-account</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateTariffContacts"
                  :label="'Контакты'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-file-code</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateTariffCode"
                  :label="'Code'"
                  class="purple-input"
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
                  :loading="loadingUpdateTariff"
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
      v-model="dialogProviders"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Текущие провайдеры

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogProviders = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-data-table
            :headers="providerHeaders"
            :items="currentProviders"
            hide-default-footer
          >
            <template v-slot:item.on="{ item }">
              <v-switch :readonly="loadingDomainUpdate" :input-value="item.on" class="ma-2" label="On" @change="toggleDomain(item)" />
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon small color="error" @click="deleteProviderFromCabinet(item)">
                mdi-delete
              </v-icon>
            </template>
          </v-data-table>
        </v-card-text>

        <v-card-text>
          <v-row align="center">
            <v-col cols="8">
              <v-select
                v-model="newCurrentProvider"
                :items="filteredProviders"
                item-text="name"
                item-value="id"
                label="Провайдер"
              />
            </v-col>
            <v-col cols="4">
              <v-btn :disabled="!newCurrentProvider" color="primary" @click="addProviderToCabinet">
                Добавить
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  layout: 'Entrance',

  async fetch () {
    await this.$store.dispatch('cabinet/getAll')
    await this.$store.dispatch('tariff/getAll')
  },

  data () {
    return {
      dialogTariff: false,
      dialogUpdateTariff: false,
      loadingUpdateTariff: false,
      updateTariffId: null,
      updateTariffName: null,
      updateTariffPrice: null,
      updateTariffContacts: null,
      updateTariffCode: null,
      createFromDateMenu: false,
      createToDateMenu: false,
      updateFromDateMenu: false,
      updateToDateMenu: false,
      updateDateTo: false,
      dialogCreate: false,
      createName: null,
      createAmount: '0.00',
      loadingCreate: false,
      dialogProviders: false,
      currentProviders: [],
      currentCabinet: null,
      selectedDomain: null,
      dialogUpdate: false,
      dialogDomain: false,
      dialogCampaign: false,
      dialogCreateCampaign: false,
      dialogUpdateCampaign: false,
      dialogDeleteCampaign: false,
      loadingDeleteCampaign: false,
      loadingUpdateCampaign: false,
      loadingCreateCampaign: false,
      deleteCampaignId: null,
      deleteCampaignCabinetId: null,
      currentDomains: [],
      currentCampaigns: [],
      updateName: null,
      updateAmount: '0.00',
      updateId: null,
      updateCampaignId: null,
      updateCampaignTariffId: null,
      updateCampaignDiscount: '0.00',
      updateCampaignFrom: null,
      updateCampaignTo: null,
      updateCampaignBalance: 0,
      createCampaignTariffId: null,
      createCampaignDiscount: '0.00',
      createCampaignFrom: null,
      createCampaignTo: null,
      loadingUpdate: false,
      loadingTable: false,
      newCurrentProvider: null,
      loadingDomainUpdate: false,
      providerHeaders: [
        {
          sortable: false,
          align: 'center',
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          align: 'center',
          text: 'Имя',
          value: 'name'
        },
        {
          sortable: false,
          align: 'center',
          text: 'Действия',
          value: 'actions'
        }
      ],
      domainHeaders: [
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
          text: 'Статус',
          value: 'on',
          align: 'right'
        }
      ],
      tariffHeaders: [
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
          text: 'Цена',
          value: 'price',
          align: 'right'
        },
        {
          sortable: false,
          text: 'Контакты',
          value: 'contacts',
          align: 'right'
        },
        {
          sortable: false,
          text: 'Редактировать',
          value: 'edit',
          align: 'center'
        }
      ],
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
          text: 'Домены',
          value: 'domains'
        },
        {
          sortable: false,
          text: 'Поставщики данных',
          value: 'providers',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Редактировать поставщиков',
          value: 'providersSetting',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Настройки',
          value: 'settings',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Войти',
          value: 'enter',
          align: 'center'
        }
      ]
    }
  },

  computed: {
    cabinets () {
      return this.$store.getters['cabinet/list']
    },

    tariffs () {
      return this.$store.getters['tariff/list']
    },

    filteredProviders () {
      return this.$store.getters['provider/list'].filter((val) => {
        if (!this.currentProviders) {
          return true
        }
        return !this.currentProviders.find((valCur) => {
          return valCur.id === val.id
        })
      })
    },

    nameErrors () {
      const errors = []
      if (!this.$v.createName.$dirty) {
        return errors
      }

      !this.$v.createName.required && errors.push(this.$t('auth.validate.require'))
      return errors
    },
    nameUpdateErrors () {
      const errors = []
      if (!this.$v.updateName.$dirty) {
        return errors
      }

      !this.$v.updateName.required && errors.push(this.$t('auth.validate.require'))
      return errors
    }
  },

  validations: {
    updateName: {
      required
    },
    createName: {
      required
    }
  },

  mounted () {
    if (!this.$cookies.get('access-token')) {
      this.$routeri18n.push('Login')
    }
  },

  methods: {
    dataClear () {
      this.createName = null
      this.updateName = null
      this.updateId = null
      this.currentProviders = []
      this.newCurrentProvider = null
      this.currentCabinet = null
    },

    pad (s) {
      return (s < 10) ? '0' + s : s
    },

    dateFormat (item) {
      if (item === null) {
        return 'Нет данных'
      }

      const d = new Date(item)
      return [this.pad(d.getDate()), this.pad(d.getMonth() + 1), d.getFullYear()].join('/') + ' ' + d.getHours() + ':' + d.getMinutes()
    },

    timeFilter (item) {
      if (item === null) {
        return false
      }

      const now = new Date()
      const minPeriod = new Date(now.valueOf() - 1000 * 60 * 60 * 3)
      const lastActivity = new Date(item)

      return lastActivity.valueOf() >= minPeriod.valueOf()
    },

    editTariff (item) {
      this.dialogUpdateTariff = true
      this.updateTariffId = item.id
      this.updateTariffName = item.name
      this.updateTariffPrice = item.price
      this.updateTariffContacts = item.contacts
      this.updateTariffCode = item.code
    },
    tryUpdateTariff () {
      this.loadingUpdateTariff = true
      this.$store.dispatch('tariff/update', {
        id: this.updateTariffId,
        name: this.updateTariffName,
        price: this.updateTariffPrice,
        contacts: this.updateTariffContacts,
        code: this.updateTariffCode
      }).then(() => {
        this.$store.dispatch('tariff/getAll')
        this.$notify.show('Тариф обновлен', 'success')
      }).catch(() => {
        this.$notify.show('Ошибка при обновлении тарифа', 'error')
      }).finally(() => {
        this.loadingUpdateTariff = false
        this.dialogUpdateTariff = false
      })
    },

    cabinetProviders (item) {
      this.dialogProviders = true
      this.$store.dispatch('provider/getAll')
      this.currentProviders = item.providers
      this.currentCabinet = item
    },

    addProviderToCabinet () {
      this.$store.dispatch('cabinet/attach', {
        id: this.currentCabinet.id,
        providerId: this.newCurrentProvider
      }).then(() => {
        this.$store.dispatch('cabinet/getAll')
        this.$notify.show(this.$t('cabinets.notify.update.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('cabinets.notify.update.error'), 'error')
      }).finally(() => {
        // this.dataClear()
        this.dialogProviders = false
      })
    },

    deleteProviderFromCabinet (item) {
      this.$store.dispatch('cabinet/detach', {
        id: this.currentCabinet.id,
        providerId: item.id
      }).then(() => {
        this.$store.dispatch('cabinet/getAll')
        this.$notify.show(this.$t('cabinets.notify.update.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('cabinets.notify.update.error'), 'error')
      }).finally(() => {
        this.dataClear()
        this.dialogProviders = false
      })
    },

    cabinetUpdate (item) {
      this.updateName = item.name
      this.updateId = item.id
      this.dialogUpdate = true
    },

    openDialogDomain (item) {
      this.getCurrentDomains(item)
      this.dialogDomain = true
    },

    getCurrentCampaigns (item) {
      this.$store.dispatch('campaign/getAll', { cabinetId: item.cabinet_id, domainId: item.id }).then(() => {
        this.currentCampaigns = this.$store.getters['campaign/list']
      })
    },

    getCurrentDomains (item) {
      this.$store.dispatch('domain/getAll', { cabinetid: item.id }).then(() => {
        this.currentDomains = this.$store.getters['domain/list']
      })
    },

    tryUpdateCabinet () {
      this.loadingUpdate = true
      const updateData = {
        name: this.updateName
      }

      this.$store.dispatch('cabinet/update', {
        data: updateData,
        id: this.updateId
      }).then(() => {
        this.$store.dispatch('cabinet/getAll')
        this.$notify.show(this.$t('cabinets.notify.update.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('cabinets.notify.update.error'), 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingUpdate = false
        this.dialogUpdate = false
      })
    },

    tryCreateCabinet () {
      this.loadingCreate = true
      const createData = {
        name: this.createName
      }

      this.$store.dispatch('cabinet/create', createData).then(() => {
        this.$store.dispatch('cabinet/getAll')
        this.$notify.show(this.$t('cabinets.notify.create.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('cabinets.notify.create.error'), 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingCreate = false
        this.dialogCreate = false
      })
    },

    toggleDomain (domain) {
      this.loadingDomainUpdate = true
      this.$store.dispatch('domain/update', {
        cabinetid: domain.cabinet_id,
        id: domain.id,
        data: {
          name: domain.name,
          token: domain.token,
          domain: domain.domain,
          api_url: domain.api_url,
          geo_on: domain.geo_on,
          jsBefore: domain.js_before,
          jsAfter: domain.js_after,
          on: !domain.on
        }
      }).then(() => {
        this.loadingTable = true
        this.$store.dispatch('cabinet/getAll').finally(() => {
          this.loadingTable = false
          this.loadingDomainUpdate = false
        })
        this.$notify.show(this.$t('domains.notify.update.success'), 'success')
      }).catch(() => {
        this.loadingDomainUpdate = false
        this.$notify.show(this.$t('domains.notify.update.error'), 'error')
      })
    }
  }
}
</script>

<style scoped>
.cabinet-name .amount {
  color: #02982b;
}

.cabinet-name .amount-block {
  font-size: 1.4rem;
}

.cabinet-name {
  cursor: pointer;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  flex-direction: column;
}

.circleGreen {
  height: 9px;
  width: 9px;
  border: 2px solid green;
  border-radius: 25px;
  background-color: green;
  box-shadow: 0 0 5px rgb(147, 194, 147);
}
.circleOrange {
  height: 9px;
  width: 9px;
  border: 2px solid orange;
  border-radius: 25px;
  background-color: orange;
  box-shadow: 0 0 5px rgb(238, 209, 155);
}
.circleRed {
  height: 9px;
  width: 9px;
  border: 2px solid red;
  border-radius: 25px;
  background-color: red;
  box-shadow: 0 0 5px rgb(223, 130, 130);
}
.statusCircle {
  cursor: pointer;
}
.statusText {
  display: flex;
  align-items: flex-end;
}
</style>
