<template>
  <v-container
    fluid
    tag="section"
  >
    <BaseMaterialCard
      color="warning"
      class="px-5 py-3"
      light
    >
      <template v-slot:customHeading>
        <v-col class="pa-0" cols="12">
          <v-card-title class="display-2 font-weight-light pb-0">
            Биллинг
            <v-spacer />

            <v-col class="pa-0" cols="3" align-self="center">
              <v-menu
                v-model="searchFromDateMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="searchCampaignFrom"
                      label="Действует от"
                      prepend-icon="mdi-calendar"
                      readonly
                      class="mr-2"
                      hide-details
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="searchCampaignFrom"
                  locale="ru-in"
                  no-title
                  label="Действует от"
                  @input="getAllCampaign"
                />
              </v-menu>
            </v-col>
            <v-col cols="3" align-self="center" class="pa-0">
              <v-menu
                v-model="searchToDateMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="searchCampaignTo"
                      label="Действует до"
                      prepend-icon="mdi-calendar"
                      readonly
                      class="mr-2"
                      hide-details
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="searchCampaignTo"
                  locale="ru-in"
                  no-title
                  label="Действует до"
                  @input="getAllCampaign"
                />
              </v-menu>
            </v-col>
          </v-card-title>
          <v-card-title class="display-2 font-weight-light py-0">
            <v-col cols="3">
              <v-select
                v-model="searchCabinet"
                :items="cabinets"
                item-text="name"
                item-value="id"
                return-object
                label="Кабинет"
                @input="selectCabinetEvent"
              />
            </v-col>
            <v-col v-if="searchCabinet && searchCabinet.domains" cols="2">
              <v-select
                v-model="searchDomain"
                :items="searchCabinet.domains"
                item-text="name"
                item-value="id"
                return-object
                label="Домен"
                @input="selectDomainEvent"
              />
            </v-col>
            <v-btn text @click="clearSearch">
              Очистить
            </v-btn>
            <v-btn text @click="getAllCampaign">
              Применить
            </v-btn>
            <v-btn text @click="openCreateDialog">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-card-title>
        </v-col>
      </template>

      <v-container>
        <v-data-table
          :headers="campaignHeaders"
          :items="campaigns"
          :loading="loadingTable"
        >
          <template v-slot:item.tariff="{ item }">
            <div>{{ item.tariff.name }}</div>
          </template>
          <template v-slot:item.сurrentBalance="{ item }">
            <div>{{ item.tariff.contacts }}</div>
          </template>
          <template v-slot:item.domain="{ item }">
            <div>{{ item.domain.name }}</div>
          </template>
          <template v-slot:item.cabinet="{ item }">
            <div>{{ item.domain.cabinet.name }}</div>
          </template>
          <template v-slot:item.billing="{ item }">
            <v-btn text @click="openBilling(item)">
              <v-icon>mdi-eye</v-icon>
            </v-btn>
          </template>
          <template v-slot:item.create_bill="{ item }">
            <v-btn text @click="openCreateBillDialog(item)">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </template>
          <template v-slot:item.cost="{ item }">
            <v-col
              cols="12"
              class="text-right"
            >
              {{ (item.tariff.price * (100 - item.discount)/100) }}
            </v-col>
          </template>
          <template v-slot:item.delete="{ item }">
            <v-btn icon>
              <v-icon
                small
                color="error"
                @click="openDeleteCampaignDialog(item)"
              >
                mdi-delete
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:item.edit="{ item }">
            <v-btn
              v-if="item.tariff_id && (item.tariff.code === 'custom' || item.tariff.code === 'personal')"
              icon
            >
              <v-icon
                small
                @click="openEditCampaign(item)"
              >
                mdi-pencil
              </v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-container>
    </BaseMaterialCard>

    <v-dialog
      v-model="dialogCreateCampaign"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Создать услугу
          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogCreateCampaign = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryCreateCampaign">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-c-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-select
                  v-model="createCampaignCabinet"
                  :items="cabinets"
                  return-object
                  item-text="name"
                  item-value="id"
                  label="Кабинет"
                  @input="selectCabinetEventCreateCampaign"
                />
              </v-col>
              <v-col v-if="createCampaignCabinet && createCampaignCabinet.domains" cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-c-box</v-icon>
              </v-col>
              <v-col v-if="createCampaignCabinet && createCampaignCabinet.domains" cols="10" class="py-0">
                <v-select
                  v-model="createCampaignDomain"
                  :items="createCampaignCabinet.domains"
                  return-object
                  item-text="name"
                  item-value="id"
                  label="Домен"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-select
                  v-model="createCampaignTariffId"
                  :items="tariffs"
                  item-text="name"
                  item-value="id"
                  label="Тариф"
                />
              </v-col>
              <v-col v-if="$store.getters['tariff/getById'](createCampaignTariffId) ? $store.getters['tariff/getById'](createCampaignTariffId).code === 'personal' : false " cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col v-if="$store.getters['tariff/getById'](createCampaignTariffId) ? $store.getters['tariff/getById'](createCampaignTariffId).code === 'personal' : false " cols="10" class="py-0">
                <v-text-field
                  v-model="createCampaignContacts"
                  :label="'Контакты'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col v-if="$store.getters['tariff/getById'](createCampaignTariffId) ? $store.getters['tariff/getById'](createCampaignTariffId).code === 'personal' : false " cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col v-if="$store.getters['tariff/getById'](createCampaignTariffId) ? $store.getters['tariff/getById'](createCampaignTariffId).code === 'personal' : false " cols="10" class="py-0">
                <v-text-field
                  v-model="createCampaignPrice"
                  :label="'Цена'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="name"
                  v-model="createCampaignDiscount"
                  :label="'скидка'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="createFromDateMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="createCampaignFrom"
                      label="Действует от"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="createCampaignFrom"
                  locale="ru-in"
                  no-title
                  label="Действует от"
                />
              </v-menu>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="createToDateMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="createCampaignTo"
                      label="Действует до"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="createCampaignTo"
                  locale="ru-in"
                  no-title
                  label="Действует до"
                />
              </v-menu>
              <v-col
                cols="12"
                class="text-right"
              >
                <v-btn
                  color="primary"
                  class="mr-0"
                  type="submit"
                  :disabled="!createCampaignCabinet || !createCampaignDomain || !createCampaignFrom
                    || !createCampaignTo"
                  :loading="loadingCreateCampaign"
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
      v-model="dialogEditCampaign"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Услуга
          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogEditCampaign = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryEditCampaign">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-select
                  v-model="editCampaignTariffId"
                  :items="tariffs"
                  item-text="name"
                  item-value="id"
                  label="Тариф"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="name"
                  v-model="editCampaignBalance"
                  :label="'Баланс'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-n-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  ref="name"
                  v-model="editCampaignDiscount"
                  :label="'скидка'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="editFromDateMenu"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="editCampaignFrom"
                      label="Действует от"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="editCampaignFrom"
                  locale="ru-in"
                  no-title
                  label="Действует от"
                />
              </v-menu>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="editToDateMenu"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="editCampaignTo"
                      label="Действует до"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="editCampaignTo"
                  locale="ru-in"
                  no-title
                  label="Действует до"
                />
              </v-menu>
              <v-col
                cols="12"
                class="text-right"
              >
                <v-btn
                  color="primary"
                  class="mr-0"
                  type="submit"
                  :loading="loadingEditCampaign"
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
      v-model="dialogDeleteCampaign"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Удалить кампнию

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogDeleteCampaign = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-col cols="12" align-self="center">
            Вы действительно хотите удалить эту улугу?
          </v-col>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="error"
            text
            type="submit"
            :loading="loadingDeleteCampaign"
            @click="tryDeleteCampaign"
          >
            {{ $t('delete') }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="dialogCreateBill"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Создать счет
          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogCreateBill = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryCreateBill">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="createBillDateMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="createBillDate"
                      label="Дата прихода"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="createBillDate"
                  locale="ru-in"
                  no-title
                  label="Дата прихода"
                />
              </v-menu>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-currency-usd</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="createBillArrival"
                  :label="'Приход'"
                  type="number"
                  class="purple-input"
                />
              </v-col>
              <v-col
                cols="12"
                class="text-right"
              >
                <v-btn
                  :disabled="createBillArrival === null"
                  color="primary"
                  class="mr-0"
                  type="submit"
                  :loading="loadingCreateBill"
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
      v-model="dialogShowBill"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Приходы
          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogShowBill = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text>
          <v-data-table
            :headers="paymentHeaders"
            :items="billing"
            :loading="loadingTable"
          >
            <template v-slot:item.arrival="{ item }">
              <span style="color: darkgreen; margin-right: 20px;">+ {{ item.arrival }} руб.</span>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'Billing',

  async fetch () {
    await this.$store.dispatch('campaign/getAllAdmin', {})
    await this.$store.dispatch('cabinet/getAll')
    await this.$store.dispatch('tariff/getAll')
  },

  data () {
    return {
      paymentHeaders: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          text: 'Приход',
          value: 'arrival'
        },
        {
          sortable: false,
          text: 'Дата прихода',
          value: 'arrival_at'
        },
        {
          sortable: false,
          text: 'Дата создания',
          value: 'created_at'
        }
      ],
      campaignHeaders: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          text: 'Тариф',
          value: 'tariff'
        },
        {
          sortable: false,
          text: 'Домен',
          value: 'domain'
        },
        {
          sortable: false,
          text: 'Кабинет',
          value: 'cabinet'
        },
        {
          sortable: false,
          text: 'Контакты',
          value: 'balance'
        },
        {
          sortable: false,
          text: 'Выделено контактов',
          value: 'сurrentBalance'
        },
        {
          sortable: false,
          text: 'Скидка',
          value: 'discount'
        },
        {
          sortable: false,
          text: 'Цена',
          value: 'cost'
        },
        {
          sortable: false,
          text: 'Действует от',
          value: 'from'
        },
        {
          sortable: false,
          text: 'Действует до',
          value: 'to'
        },
        {
          sortable: false,
          text: 'Удалить',
          value: 'delete'
        },
        {
          sortable: false,
          text: 'Редактировать',
          value: 'edit'
        },
        {
          sortable: false,
          text: 'Счета',
          value: 'billing'
        },
        {
          sortable: false,
          text: 'Создать счет',
          value: 'create_bill'
        },
        {
          sortable: false,
          text: 'Дата создания',
          value: 'created_at'
        }
      ],
      expanded: [],
      loadingTable: false,
      dialogCreateBill: false,
      createBillArrival: null,
      createBillDateMenu: false,
      createBillDate: null,
      loadingCreateBill: false,
      searchCabinet: null,
      searchDomain: null,
      searchCampaign: null,
      searchCampaignFrom: null,
      searchCampaignTo: null,
      searchFromDateMenu: false,
      searchToDateMenu: false,
      createBillCabinet: null,
      createBillDomain: null,
      createBillCampaign: null,
      showBillCabinet: null,
      showBillDomain: null,
      showBillCampaign: null,
      dialogShowBill: false,
      editCampaignId: null,
      editCampaignDomain: null,
      editCampaignCabinet: null,
      editCampaignTariffId: null,
      editCampaignBalance: null,
      editCampaignDiscount: null,
      editCampaignFrom: null,
      editCampaignTo: null,
      loadingEditCampaign: false,
      editFromDateMenu: false,
      editToDateMenu: false,
      dialogEditCampaign: false,
      createCampaignDomain: null,
      createCampaignCabinet: null,
      createCampaignTariffId: null,
      createCampaignPrice: null,
      createCampaignContacts: null,
      createCampaignBalance: null,
      createCampaignDiscount: 0,
      createCampaignFrom: null,
      createCampaignTo: null,
      loadingCreateCampaign: false,
      createFromDateMenu: false,
      createToDateMenu: false,
      dialogCreateCampaign: false,
      deleteCampaignCabinet: null,
      deleteCampaignDomain: null,
      deleteCampaignId: null,
      loadingDeleteCampaign: false,
      dialogDeleteCampaign: false
    }
  },

  computed: {
    campaigns () {
      return this.$store.getters['campaign/list']
    },
    cabinets () {
      return this.$store.getters['cabinet/list']
    },
    billing () {
      return this.$store.getters['billing/list']
    },
    tariffs () {
      return this.$store.getters['tariff/list']
    }
  },

  methods: {
    openBilling (item) {
      this.$store.commit('billing/setList', [])
      this.showBillCampaign = item.id
      this.showBillDomain = item.domain.id
      this.showBillCabinet = item.domain.cabinet_id
      this.getAllBill()
      this.dialogShowBill = true
    },
    openCreateBillDialog (item) {
      this.createBillCampaign = item.id
      this.createBillDomain = item.domain.id
      this.createBillCabinet = item.domain.cabinet_id
      this.dialogCreateBill = true
    },

    openEditCampaign (item) {
      this.editCampaignId = item.id
      this.editCampaignDomain = item.domain.id
      this.editCampaignCabinet = item.domain.cabinet_id
      this.editCampaignTariffId = item.tariff.id
      this.editCampaignBalance = item.balance
      this.editCampaignDiscount = item.discount
      this.editCampaignFrom = item.from
      this.editCampaignTo = item.to
      this.dialogEditCampaign = true
    },

    openCreateDialog () {
      this.dialogCreateCampaign = true
    },

    openDeleteCampaignDialog (item) {
      this.deleteCampaignCabinet = item.domain.cabinet_id
      this.deleteCampaignDomain = item.domain.id
      this.deleteCampaignId = item.id
      this.dialogDeleteCampaign = true
    },

    tryEditCampaign () {
      this.loadingEditCampaign = true
      this.$store.dispatch('campaign/update', {
        cabinetId: this.editCampaignCabinet,
        domainId: this.editCampaignDomain,
        campaignId: this.editCampaignId,
        tariffId: this.editCampaignTariffId,
        balance: this.editCampaignBalance,
        discount: this.editCampaignDiscount,
        from: this.editCampaignFrom,
        to: this.editCampaignTo
      }).then(() => {
        this.getAllCampaign()
        this.$notify.show('Услуга обновлена', 'success')
        this.editCampaignId = null
        this.editCampaignDomain = null
        this.editCampaignCabinet = null
        this.editCampaignTariffId = null
        this.editCampaignBalance = null
        this.editCampaignDiscount = null
        this.editCampaignFrom = null
        this.editCampaignTo = null
      }).catch(() => {
        this.$notify.show('Произошла ошибка', 'error')
      }).finally(() => {
        this.loadingEditCampaign = false
        this.dialogEditCampaign = false
      })
    },

    tryCreateCampaign () {
      this.loadingCreateCampaign = true
      this.$store.dispatch('campaign/store', {
        cabinetId: this.createCampaignCabinet.id,
        domainId: this.createCampaignDomain.id,
        tariffId: this.createCampaignTariffId,
        price: this.createCampaignPrice,
        contacts: this.createCampaignContacts,
        discount: this.createCampaignDiscount,
        from: this.createCampaignFrom + ' 00:00:00',
        to: this.createCampaignTo + ' 23:59:59'
      }).then(() => {
        this.getAllCampaign()
        this.$notify.show('Услуга создана', 'success')
        this.createCampaignDomain = null
        this.createCampaignCabinet = null
        this.createCampaignTariffId = null
        this.createCampaignContacts = null
        this.createCampaignPrice = null
        this.createCampaignDiscount = 0
        this.createCampaignFrom = null
        this.createCampaignTo = null
      }).catch(() => {
        this.$notify.show('Произошла ошибка', 'error')
      }).finally(() => {
        this.loadingCreateCampaign = false
        this.dialogCreateCampaign = false
      })
    },

    tryDeleteCampaign () {
      this.loadingDeleteCampaign = true
      this.$store.dispatch('campaign/delete', {
        cabinetId: this.deleteCampaignCabinet,
        domainId: this.deleteCampaignDomain,
        campaignId: this.deleteCampaignId
      }).then(() => {
        this.deleteCampaignCabinet = null
        this.deleteCampaignDomain = null
        this.deleteCampaignId = null
        this.getAllCampaign()
        this.$notify.show('Услуга успешно удалена', 'success')
      }).catch(() => {
        this.$notify.show('Произошла ошибка при удалении', 'error')
      }).finally(() => {
        this.loadingDeleteCampaign = false
        this.dialogDeleteCampaign = false
      })
    },

    tryCreateBill () {
      this.loadingCreateBill = true
      this.$store.dispatch('billing/store', {
        cabinetId: this.createBillCabinet,
        domainId: this.createBillDomain,
        campaignId: this.createBillCampaign,
        arrival: this.createBillArrival,
        date: this.createBillDate
      }).then(() => {
        this.getAllCampaign()
        this.$notify.show('Счет успешно создан', 'success')
        this.createBillCabinet = null
        this.createBillDomain = null
        this.createBillCampaign = null
        this.createBillArrival = null
        this.createBillDate = null
      }).catch(() => {
        this.$notify.show('Произошла ошибка при создании счета', 'error')
      }).finally(() => {
        this.loadingCreateBill = false
        this.dialogCreateBill = false
      })
    },
    selectCabinetEventCreateCampaign () {
      this.createCampaignDomain = null
    },
    selectCabinetEvent () {
      this.searchDomain = null
      this.getAllCampaign()
    },
    selectDomainEvent () {
      this.getAllCampaign()
    },
    selectCampaignEvent () {
      this.getAllCampaign()
    },
    clearSearch () {
      this.searchDomain = null
      this.searchCabinet = null
      this.searchCampaign = null
      this.searchCampaignFrom = null
      this.searchCampaignTo = null
      this.getAllCampaign()
    },

    getAllCampaign () {
      this.loadingTable = true
      this.$store.dispatch('campaign/getAllAdmin', {
        domainId: this.searchDomain ? this.searchDomain.id : null,
        cabinetId: this.searchCabinet ? this.searchCabinet.id : null,
        from: this.searchCampaignFrom,
        to: this.searchCampaignTo
      }).finally(() => {
        this.loadingTable = false
      })
    },
    getAllBill () {
      this.$store.dispatch('billing/getAll', {
        domainId: this.showBillDomain ? this.showBillDomain : null,
        cabinetId: this.showBillCabinet ? this.showBillCabinet : null,
        campaignId: this.showBillCampaign ? this.showBillCampaign : null
      })
    }
  }
}
</script>

<style scoped>
</style>
