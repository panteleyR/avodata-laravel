<template>
  <v-container>
    <v-row>
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
          class="px-5 py-3"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="pb-0 display-2 font-weight-light">
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
            </v-col>
          </template>
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="listLeads"
              :loading="loadingTable"
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
          />
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'Leads',

  async fetch () {
    await this.$store.dispatch('leads/getAll', { cabinetId: this.$route.params.cabinetid })
    await this.$store.dispatch('domain/getAll', { cabinetid: this.$route.params.cabinetid })
  },

  data () {
    return {
      domainId: null,
      loadingTable: false,
      dialogSessions: false,
      loadingSessionsTable: false,
      clientsDateMenu: false,
      datesClients: null,
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
    }
  },

  methods: {
    getSessions (id) {
      this.dialogSessions = true
      this.loadingSessionsTable = true
      this.$store.dispatch('leads/getUserSessions', {
        cabinetId: this.$route.params.cabinetid,
        userId: id
      }).finally(() => {
        this.loadingSessionsTable = false
      })
    },
    selectClientsEvent () {
      if (this.datesClients.length !== 1) {
        this.clientsDateMenuDateMenu = false
        this.loadingTable = true
        this.$store.dispatch('leads/getAll', {
          data: {
            from: this.datesClients ? this.datesClients[0] + ' 00:00:00' : null,
            to: this.datesClients ? this.datesClients[1] + ' 23:59:59' : null,
            domainId: this.domainId
          },
          cabinetId: this.$route.params.cabinetid
        }).finally(() => {
          this.loadingTable = false
        })
      }
    },

    getCsv () {
      this.$axios.get('px/v1/public/' + this.$route.params.cabinetid + '/users/csv', {
        params: {
          from: this.datesClients ? this.datesClients[0] + ' 00:00:00' : null,
          to: this.datesClients ? this.datesClients[1] + ' 23:59:59' : null,
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
      })
    }
  }
}
</script>

<style scoped>

</style>
