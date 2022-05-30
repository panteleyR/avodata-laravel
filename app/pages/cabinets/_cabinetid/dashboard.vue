<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-row>
      <v-col v-if="false" class="" cols="12">
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3 mt-0"
        >
          <template v-slot:customHeading>
            <v-card-title class="pa-0 display-2 font-weight-light" style="width: 100%">
              <v-menu
                v-model="clientsDateMenu"
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
                  <v-col class="pa-0" cols="4">
                    <v-text-field
                      v-model="dateClientsRangeText"
                      label="Даты"
                      prepend-icon="mdi-calendar"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="datesClients"
                  locale="ru-in"
                  no-title
                  label="За последний месяц"
                  range
                  @input="selectClientsEvent"
                />
              </v-menu>
              <v-col class="pa-0" cols="3">
                <v-select
                  v-model="domainId"
                  :items="domains"
                  item-text="name"
                  item-value="id"
                  label="Домен"
                  @input="selectClientsEvent"
                />
              </v-col>
              <v-spacer />
              <v-btn icon @click="getCsv()">
                <v-icon>mdi-file-download-outline</v-icon>
              </v-btn>
            </v-card-title>
          </template>
        </BaseMaterialCard>
      </v-col>
      <v-col
        v-if="false"
        cols="12"
      >
        <BaseMaterialChartCard
          v-if="this.$store.getters['leads/statistics']"
          :data="sessionsMonthStatistics.data"
          :options="sessionsMonthStatistics.options"
          color="#fff"
          hover-reveal
          type="Bar"
          class="ma-0"
        >
          <template v-slot:reveal-actions>
            <v-tooltip bottom>
              <template v-slot:activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  color="info"
                  icon
                  v-on="on"
                >
                  <v-icon
                    color="info"
                  >
                    mdi-refresh
                  </v-icon>
                </v-btn>
              </template>

              <span>Refresh</span>
            </v-tooltip>

            <v-tooltip bottom>
              <template v-slot:activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  light
                  icon
                  v-on="on"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>

              <span>Change Date</span>
            </v-tooltip>
          </template>

          <h4 class="card-title font-weight-light mt-2 ml-2">
            Визиты
          </h4>

          <p class="d-inline-flex font-weight-light ml-2 mt-1">
            Результаты последней кампании
          </p>

          <template v-slot:actions>
            <v-icon
              class="mr-1"
              small
            >
              mdi-clock-outline
            </v-icon>
            <span class="caption grey--text font-weight-light">Статистика за месяц</span>
          </template>
        </BaseMaterialChartCard>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/statistics']"
          color="info"
          icon="mdi-account"
          title="Клиенты"
          :value="this.$store.getters['leads/statistics'].users"
          sub-icon="mdi-clock"
          sub-text="Определенные пользователи"
        />
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/statistics']"
          color="primary"
          icon="mdi-poll"
          title="Посещения"
          :value="this.$store.getters['leads/statistics'].sessions"
          sub-icon="mdi-tag"
          sub-text="Количество заходов и переходов по вашим сайтам"
        />
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/statistics']"
          color="#02982b"
          icon="mdi-percent"
          title="Конверсия"
          :value="conversion"
          sub-icon="mdi-tag"
          sub-text="Конверсия определений по вашим сайтам"
        />
      </v-col>

      <v-col
        cols="12"
      >
        <BaseMaterialCard
          v-if="false"
          class="px-5 py-5 my-0"
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
          color="warning"
          class="px-5 py-3 mt-0"
        >
          <template v-slot:customHeading>
            <v-card-title class="pa-0 display-2 font-weight-light" style="width: 100%">
              <v-menu
                v-model="clientsDateMenuFrom"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="300px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col class="pa-0" cols="3">
                    <v-text-field
                      v-model="datesClientsFrom"
                      label="От"
                      prepend-icon="mdi-calendar"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="datesClientsFrom"
                  locale="ru-in"
                  no-title
                  label="За последний месяц"
                  @input="selectClientsEvent"
                />
              </v-menu>
              <v-menu
                v-model="clientsDateMenuTo"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                max-width="300px"
                min-width="220px"
                light
                color="white"
                class="bg-white"
              >
                <template v-slot:activator="{ on }">
                  <v-col class="pa-0" cols="3">
                    <v-text-field
                      v-model="datesClientsTo"
                      label="До"
                      prepend-icon="mdi-calendar"
                      readonly
                      class="mr-2"
                      v-on="on"
                    />
                  </v-col>
                </template>
                <v-date-picker
                  v-model="datesClientsTo"
                  locale="ru-in"
                  no-title
                  label="За последний месяц"
                  @input="selectClientsEvent"
                />
              </v-menu>
              <v-spacer />
              <v-btn text @click="getCsv()">
                <v-progress-circular v-if="loadingCsv" :size="24" indeterminate />
                <v-icon v-else>
                  mdi-file-download-outline
                </v-icon>
                <div class="ml-2">
                  Выгрузка csv
                </div>
              </v-btn>
            </v-card-title>
            <v-card-title class="pb-0 display-2 font-weight-light" style="width: 100%">
              <v-col class="pa-0" cols="3">
                <v-select
                  v-model="domainId"
                  :items="domains"
                  item-text="name"
                  item-value="id"
                  label="Домен"
                  @input="selectClientsEvent"
                />
              </v-col>
              <v-spacer />
              <v-btn text @click="clearSearch">
                Очистить
              </v-btn>
              <v-btn text @click="getAllClients">
                Применить
              </v-btn>
            </v-card-title>
          </template>
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="listLeads"
              :loading="loadingTable"
              :server-items-length="totalPage"
              :options.sync="options"
            >
              <template v-slot:item.contacts="{ item }">
                <div>
                  <div v-for="(contact, key) in item.contacts" :key="key">
                    <div>{{ contact.value }}</div>
                  </div>
                </div>
              </template>
              <template v-slot:item.last_contact="{ item }">
                <div>{{ item.contacts[item.contacts.length - 1].created_at }}</div>
              </template>
              <template v-slot:item.sessions="{ item }">
                <v-btn icon>
                  <v-icon
                    v-if="!$auth.isAnalytic()"
                    small
                    @click="getSessions(item.id)"
                  >
                    mdi-eye
                  </v-icon>
                </v-btn>
              </template>
              <template v-slot:item.domain="{ item }">
                <div>
                  <div>
                    {{ $store.getters['domain/getById'](item.contacts[0].domain_id) ? $store.getters['domain/getById'](item.contacts[0].domain_id).domain : '' }}
                  </div>
                </div>
              </template>
            </v-data-table>
          </v-card-text>
        </BaseMaterialCard>
      </v-col>
    </v-row>
    <v-dialog
      v-model="dialogSessions"
      light
      max-width="900"
    >
      <v-card class="text-center">
        <v-card-title>
          Сессии

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogSessions = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headersSessions"
            :items="listSessions"
            :loading="loadingSessionsTable"
            :server-items-length="totalPageSessions"
            :options.sync="optionsSessions"
          />
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'Dashboard',

  async fetch () {
    await this.$store.dispatch('leads/getAll', { cabinetId: this.$route.params.cabinetid, data: { perPage: this.options.itemsPerPage, page: this.options.page } })
    await this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid })
  },

  data () {
    return {
      optionsSessions: { itemsPerPage: 10, page: 1 },
      options: { itemsPerPage: 10, page: 1 },
      dataCompletedTasksChart: {
        data: {
          labels: ['12am', '3pm', '6pm', '9pm', '12pm', '3am', '6am', '9am'],
          series: [
            [230, 750, 450, 300, 280, 240, 200, 190]
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false
          }
        }
      },
      emailsSubscriptionChart: {

      },
      domainId: null,
      loadingTable: false,
      dialogSessions: false,
      loadingSessionsTable: false,
      clientsDateMenu: false,
      clientsDateMenuFrom: false,
      clientsDateMenuTo: false,
      datesClients: null,
      datesClientsFrom: null,
      datesClientsTo: null,
      loadingCsv: false,
      currentSessionsUserId: null,
      headers: [
        {
          text: 'Последнее определение',
          value: 'last_contact',
          sortable: false,
          align: 'left'
        },
        {
          text: 'Контакты',
          value: 'contacts',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Домен',
          value: 'domain',
          sortable: false,
          align: 'right'
        },
        {
          sortable: false,
          text: 'ID',
          value: 'id',
          align: 'right'
        },
        {
          text: 'Сессии',
          value: 'sessions',
          sortable: false,
          align: 'right'
        }
      ],
      headersSessions: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          text: 'Домен',
          value: 'domain',
          sortable: false,
          align: 'right'
        },
        {
          text: 'ref',
          value: 'ref',
          sortable: false,
          align: 'right'
        },
        {
          text: 'api_data',
          value: 'api_data',
          sortable: false,
          align: 'right'
        },
        {
          text: 'title',
          value: 'title',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Дата создания',
          value: 'created_at',
          sortable: false,
          align: 'right'
        }
      ]
    }
  },

  computed: {
    sessions () {
      return 'saf'
    },

    conversion () {
      return this.$store.getters['leads/statistics'] !== 0 && this.$store.getters['leads/statistics'].users
        ? +(
          (this.$store.getters['leads/statistics'].users * 100 / this.$store.getters['leads/statistics'].sessions).toFixed(2)
        ) + '%'
        : '0%'
    },

    sessionsMonthStatistics () {
      return {
        data: {
          labels: this.$store.getters['leads/sessionsMonthStatisticsLabel'],
          datasets: [
            {
              label: 'Визитов',
              pointBackgroundColor: '#861fe3',
              borderColor: '#861fe3',
              pointBorderWidth: '2',
              backgroundColor: '#8846e369',
              data: this.$store.getters['leads/sessionsMonthStatisticsData']
            },
            {
              label: 'Определений',
              pointBackgroundColor: '#02982b',
              borderColor: '#02982b',
              pointBorderWidth: '2',
              backgroundColor: '#02982b',
              fill: 'transparent',
              data: this.$store.getters['leads/usersMonthStatisticsData']
            }
          ]
        },
        options: {
          responsive: true,
          backgroundColor: 'black',
          maintainAspectRatio: false,
          scales: {
            yAxes: [{
              ticks: {
                fontColor: 'black',
                beginAtZero: true,
                padding: 10,
                stepSize: 5,
                suggestedMax: 100,
                suggestedMin: 0,
                autoSkipPadding: 50,
                steps: 2
              }
            }],
            xAxes: [{
              gridLines: {
                display: false
              },
              ticks: {
                fontColor: 'black',
                autoSkipPadding: 50
              }
            }]
          },
          legend: {
            position: 'bottom',
            align: 'start',
            labels: {
              fontColor: 'black'
            },
            display: true
          }
        }
      }
    },

    usersMonthStatistics () {
      return {
        data: {
          labels: this.$store.getters['leads/usersMonthStatisticsLabel'],
          datasets: [
            {
              label: 'Определений',
              pointBackgroundColor: 'white',
              borderColor: 'white',
              pointBorderWidth: '3',
              data: this.$store.getters['leads/usersMonthStatisticsData']
            }
          ]
        },
        options: {
          responsive: true,
          backgroundColor: 'white',
          maintainAspectRatio: false,
          scales: {
            yAxes: [{
              ticks: {
                fontColor: 'white',
                beginAtZero: true
              }
            }],
            xAxes: [{
              ticks: {
                fontColor: 'white',
                beginAtZero: true
              }
            }]
          },
          legend: {
            display: false
          }
        }
      }
    },

    listLeads () {
      return this.$store.getters['leads/list']
    },

    listSessions () {
      return this.$store.getters['leads/userSessions']
    },

    dateClientsRangeText () {
      return this.datesClients ? this.datesClients.join(' ~ ') : null
    },
    domains () {
      return this.$store.getters['domain/list']
    },
    totalPage () {
      return this.$store.getters['leads/totalPage']
    },
    lastPage () {
      return this.$store.getters['leads/lastPage']
    },
    totalPageSessions () {
      return this.$store.getters['leads/totalPageSessions']
    },
    lastPageSessions () {
      return this.$store.getters['leads/lastPageSessions']
    }
  },
  watch: {
    options: {
      handler () {
        this.$store.dispatch('leads/getAll', {
          cabinetId: this.$route.params.cabinetid, data: { perPage: this.options.itemsPerPage, page: this.options.page }
        })
      },
      deep: true
    },
    optionsSessions: {
      handler () {
        if (this.dialogSessions) {
          this.$store.dispatch('leads/getUserSessions', {
            perPage: this.optionsSessions.itemsPerPage,
            page: this.optionsSessions.page,
            userId: this.currentSessionsUserId,
            cabinetId: this.$route.params.cabinetid
          })
        }
      },
      deep: true
    }
  },

  mounted () {
    this.$store.dispatch('leads/getStatistics', {
      cabinetId: this.$route.params.cabinetid
    })
  },

  methods: {
    getSessions (userId) {
      this.currentSessionsUserId = userId
      this.dialogSessions = true
      this.loadingSessionsTable = true
      this.$store.dispatch('leads/getUserSessions', {
        cabinetId: this.$route.params.cabinetid,
        userId: this.currentSessionsUserId,
        perPage: this.optionsSessions.itemsPerPage,
        page: this.optionsSessions.page
      }).finally(() => {
        this.loadingSessionsTable = false
      })
    },

    selectClientsEvent () {
      if (this.datesClientsFrom && this.datesClientsTo) {
        if (Date.parse(this.datesClientsFrom) > Date.parse(this.datesClientsTo)) {
          const from = this.datesClientsTo
          this.datesClientsTo = this.datesClientsFrom
          this.datesClientsFrom = from
        }
        this.getAllClients()
      }
    },

    getAllClients () {
      this.loadingTable = true
      this.$store.dispatch('leads/getAll', {
        data: {
          from: this.datesClientsFrom ? this.datesClientsFrom + ' 00:00:00' : null,
          to: this.datesClientsTo ? this.datesClientsTo + ' 23:59:59' : null,
          domainId: this.domainId,
          perPage: this.options.itemsPerPage,
          page: this.options.page
        },
        cabinetId: this.$route.params.cabinetid
      }).finally(() => {
        this.loadingTable = false
      })
      this.$store.dispatch('leads/getStatistics', {
        data: {
          from: this.datesClientsFrom ? this.datesClientsFrom + ' 00:00:00' : null,
          to: this.datesClientsTo ? this.datesClientsTo + ' 23:59:59' : null,
          domainId: this.domainId
        },
        cabinetId: this.$route.params.cabinetid
      })
    },

    getCsv () {
      this.loadingCsv = true
      this.$axios.get('px/v1/public/' + this.$route.params.cabinetid + '/users/csv', {
        responseType: 'blob',
        params: {
          from: this.datesClientsFrom ? this.datesClientsFrom + ' 00:00:00' : null,
          to: this.datesClientsTo ? this.datesClientsTo + ' 23:59:59' : null,
          domainId: this.domainId
        }
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        const currentTime = new Date()
        link.href = url
        link.setAttribute('download', currentTime.getFullYear() + '-' + (currentTime.getMonth() + 1) + '-' + currentTime.getDate() + '-users.csv')
        document.body.appendChild(link)
        link.click()
      }).finally(() => {
        this.loadingCsv = false
      })
    },

    clearSearch () {
      this.datesClientsFrom = null
      this.datesClientsTo = null
      this.domainId = null
    }
  }
}
</script>
