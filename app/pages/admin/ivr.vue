<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-row>
      <v-col cols="12">
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3"
          light
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="display-2 font-weight-light">
                База прозвонов
                <v-spacer />
                <v-btn text @click="openCreate()">
                  <div class="ml-2">
                    Добавить
                  </div>
                </v-btn>
              </v-card-title>
            </v-col>
          </template>

          <v-container>
            <v-data-table
              :headers="headers"
              :items="listCaller"
              :loading="loadingTable"
            >
              <template v-slot:item.caller="{ item }">
                <div>
                  <v-btn icon>
                    <v-icon
                      small
                      @click="showPhones(item.phones)"
                    >
                      mdi-eye
                    </v-icon>
                  </v-btn>
                </div>
              </template>
            </v-data-table>
          </v-container>
        </BaseMaterialCard>
      </v-col>
    </v-row>

    <v-dialog
      v-model="dialogCreate"
      light
      max-width="800"
    >
      <v-card class="text-center">
        <v-card-title>
          Создать прозвон

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogCreate = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryCreateCaller">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-phone-settings</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-textarea
                  v-model="createPhones"
                  rows="3"
                  :label="'Номера для прозвона'"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-calendar</v-icon>
              </v-col>
              <v-menu
                v-model="createDateMenu"
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
                      v-model="createDate"
                      label="Дата прозвона"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="createDate"
                  locale="ru-in"
                  no-title
                  label="Дата прозвона"
                />
              </v-menu>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-clock</v-icon>
              </v-col>
              <v-menu
                v-model="createTimeMenu"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
                :close-on-content-click="false"
              >
                <template v-slot:activator="{ on }">
                  <v-col cols="10" class="py-0">
                    <v-text-field
                      v-model="createTime"
                      label="Время начала прозвона"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-time-picker
                  v-model="createTime"
                  format="24hr"
                  full-width
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
                  :loading="loadingCreate"
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
      v-model="dialogPhones"
      light
      max-width="800"
    >
      <v-card class="text-center">
        <v-card-title>
          Телефоны на прозвон

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogPhones = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-container class="py-0">
          <v-data-table
            :headers="headersPhones"
            :items="listPhones"
            :loading="loadingTable"
          />
        </v-container>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  data () {
    return {
      listPhones: [],
      dialogCreate: false,
      dialogPhones: false,
      loadingTablePhone: false,
      loadingTable: false,
      loadingCreate: false,
      createDateMenu: false,
      createDate: null,
      createTimeMenu: false,
      createTime: null,
      createPhones: '',
      headersPhones: [
        {
          sortable: false,
          text: 'Номер',
          value: 'number'
        },
        {
          sortable: false,
          text: 'Статус',
          value: 'status'
        }
      ],
      headers: [
        {
          sortable: false,
          text: 'Дата',
          value: 'date'
        },
        {
          sortable: false,
          text: 'Прозвоны',
          value: 'caller'
        }
      ],
      callerList: [
        {
          date: '2021-01-20 15:36:00',
          phones: [
            {
              number: '79007607883',
              status: 'Выполнено'
            },
            {
              number: '79037569516',
              status: 'Выполнено'
            },
            {
              number: '79526265418',
              status: 'Выполнено'
            },
            {
              number: '79339153612',
              status: 'Выполнено'
            },
            {
              number: '79007316154',
              status: 'Выполнено'
            },
            {
              number: '79000095257',
              status: 'Выполнено'
            }
          ]
        },
        {
          date: '2021-01-20 15:36:00',
          phones: [

            {
              number: '79008333520',
              status: 'Выполнено'
            },
            {
              number: '79004415955',
              status: 'Выполнено'
            }
          ]
        },
        {
          date: '2021-01-24 12:15:00',
          phones: [

            {
              number: '7824214214',
              status: 'Выполнено'
            },
            {
              number: '7824214214',
              status: 'Выполнено'
            },
            {
              number: '7824214214',
              status: 'Выполнено'
            },
            {
              number: '7824214214',
              status: 'Выполнено'
            },
            {
              number: '7824214214',
              status: 'Выполнено'
            },
            {
              number: '7824214214',
              status: 'Выполнено'
            }
          ]
        }
      ]

    }
  },

  computed: {
    listCaller () {
      // return this.$store.getters['caller/list']
      return this.callerList
    }
  },

  methods: {
    openCreate () {
      this.dialogCreate = true
    },

    showPhones (phones) {
      this.listPhones = phones
      this.dialogPhones = true
    },

    tryCreateCaller () {
      this.loadingCreate = true
      const date = this.createDate + ' ' + this.createTime + ':00'
      const phonesFirst = this.createPhones
      const phonesSecond = phonesFirst.split('\n')
      const phones = phonesSecond.map((el) => {
        return {
          number: el,
          status: 'В процессе'
        }
      })
      this.callerList.push({
        date,
        phones
      })
      // this.$store.dispatch('caller/store', {
      //   date: this.createDate + this.createTime,
      //   phones: this.createPhones
      // }).then(() => {
      //   this.getAllCampaign()
      //   this.$notify.show('Услуга создана', 'success')
      //   this.createCampaignDomain = null
      //   this.createCampaignCabinet = null
      //   this.createCampaignTariffId = null
      //   this.createCampaignContacts = null
      //   this.createCampaignPrice = null
      //   this.createCampaignDiscount = 0
      //   this.createCampaignFrom = null
      //   this.createCampaignTo = null
      // }).catch(() => {
      //   this.$notify.show('Произошла ошибка', 'error')
      // }).finally(() => {
      //   this.loadingCreateCampaign = false
      //   this.dialogCreateCampaign = false
      // })
      this.createDate = null
      this.createTime = null
      this.createPhones = null
      this.loadingCreate = false
      this.dialogCreate = false
    }
  }

  // async fetch () {
  //   await this.$store.dispatch('caller/getAll')
  // }
}
</script>

<style scoped>

</style>
