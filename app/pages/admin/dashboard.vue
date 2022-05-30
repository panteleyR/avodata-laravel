<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-row>
      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/rootStatistics']"
          color="info"
          icon="mdi-account"
          title="Клиенты"
          :value="this.$store.getters['leads/rootStatistics'].users"
          sub-icon="mdi-clock"
          sub-text="Определенные пользователи"
          light
        />
      </v-col>
      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/rootStatistics']"
          color="primary"
          icon="mdi-poll"
          title="Посещения"
          :value="this.$store.getters['leads/rootStatistics'].sessions"
          sub-icon="mdi-tag"
          sub-text="Количество заходов и переходов по вашим сайтам"
          light
          class="black--text"
        />
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="4"
      >
        <BaseMaterialStatsCard
          v-if="this.$store.getters['leads/rootStatistics']"
          color="#02982b"
          icon="mdi-percent"
          title="Конверсия"
          :value="conversion"
          sub-icon="mdi-tag"
          sub-text="Конверсия определений по вашим сайтам"
          light
        />
      </v-col>

      <v-col cols="12">
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3"
          light
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="display-2 font-weight-light">
                Поставщики
                <v-spacer />
                <v-col cols="3">
                  <v-menu
                    v-model="providersFromDateMenu"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                    light
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="dateProvidersFrom"
                        label="От"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="dateProvidersFrom"
                      locale="ru-in"
                      no-title
                      label="Даты"
                      @input="selectProvidersEvent"
                    />
                  </v-menu>
                </v-col>
                <v-col cols="3">
                  <v-menu
                    v-model="providersToDateMenu"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                    light
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="dateProvidersTo"
                        label="До"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="dateProvidersTo"
                      locale="ru-in"
                      no-title
                      label="Даты"
                      @input="selectProvidersEvent"
                    />
                  </v-menu>
                </v-col>
              </v-card-title>
            </v-col>
          </template>

          <v-container>
            <v-data-table
              :headers="headersProviders"
              :items="$store.getters['provider/list']"
              :loading="loadingProvidersTable"
            >
              <template v-slot:item.sended="{ item }">
                <div>
                  {{ $store.getters['leads/getRootProvidersById'](item.id) ? $store.getters['leads/getRootProvidersById'](item.id).count : '0' }}
                </div>
              </template>
              <template v-slot:item.contacts="{ item }">
                <div>
                  {{ $store.getters['leads/getRootContactsById'](item.id) ? $store.getters['leads/getRootContactsById'](item.id).count : '0' }}
                </div>
              </template>
              <template v-slot:item.conversion="{ item }">
                <div>
                  {{
                    ($store.getters['leads/getRootContactsById'](item.id)
                      && $store.getters['leads/getRootProvidersById'](item.id)
                      && $store.getters['leads/getRootProvidersById'](item.id).count !== 0
                      && $store.getters['leads/getRootContactsById'](item.id).count !== 0) ? Math.round(($store.getters['leads/getRootContactsById'](item.id).count * 100)/$store.getters['leads/getRootProvidersById'](item.id).count) : '0' }} %
                </div>
              </template>
              <template v-slot:item.forecast="{ item }">
                <div>
                  {{
                    ($store.getters['leads/getRootContactsById'](item.id)
                      && $store.getters['leads/getRootContactsById'](item.id).count !== 0) ? item.price * $store.getters['leads/getRootContactsById'](item.id).count : '0' }} Р
                </div>
              </template>
            </v-data-table>
          </v-container>
        </BaseMaterialCard>
      </v-col>

      <v-col cols="12">
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3"
          light
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="display-2 font-weight-light">
                Клиенты
                <v-spacer />
                <v-col cols="3">
                  <v-menu
                    v-model="clientsFromDateMenu"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                    light
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="dateClientsFrom"
                        label="От"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="dateClientsFrom"
                      locale="ru-in"
                      no-title
                      label="Даты"
                      @input="selectClientsEvent"
                    />
                  </v-menu>
                </v-col>
                <v-col cols="3">
                  <v-menu
                    v-model="clientsToDateMenu"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                    light
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="dateClientsTo"
                        label="До"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-on="on"
                      />
                    </template>
                    <v-date-picker
                      v-model="dateClientsTo"
                      locale="ru-in"
                      no-title
                      label="Даты"
                      @input="selectClientsEvent"
                    />
                  </v-menu>
                </v-col>
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
              <v-card-title class="display-2 font-weight-light">
                <v-col cols="3">
                  <v-select
                    v-model="clientsCabinet"
                    :items="cabinets"
                    item-text="name"
                    item-value="id"
                    return-object
                    label="Кабинет"
                    @input="selectClientsCabinetEvent"
                  />
                </v-col>
                <v-col v-if="clientsCabinet && clientsCabinet.domains" cols="2">
                  <v-select
                    v-model="clientsDomain"
                    :items="clientsCabinet.domains"
                    item-text="name"
                    item-value="id"
                    return-object
                    label="Домен"
                    @input="selectClientsDomainEvent"
                  />
                </v-col>
                <v-spacer />
                <v-btn text @click="clearClients">
                  Очистить
                </v-btn>
                <v-btn text @click="getAllClients">
                  Применить
                </v-btn>
              </v-card-title>
            </v-col>
          </template>

          <v-container>
            <v-data-table
              :headers="headers"
              :items="listLeads"
              :loading="loadingTable"
              :server-items-length="rootTotalPage"
              :options.sync="options"
            >
              <template v-slot:item.contacts="{ item }">
                <div>
                  <div v-for="(contact, key) in item.contacts" :key="key">
                    <div>{{ contact.value }}</div>
                  </div>
                </div>
              </template>
              <template v-slot:item.providers="{ item }">
                <div>
                  <div v-for="(contact, key) in item.contacts" :key="key">
                    <div>{{ $store.getters['provider/getById'](contact.provider_id) ? $store.getters['provider/getById'](contact.provider_id).name : '' }}</div>
                  </div>
                </div>
              </template>
              <template v-slot:item.domain="{ item }">
                <div>
                  <div>
                    {{ $store.getters['domain/getRootById'](item.contacts[0].domain_id) ? $store.getters['domain/getRootById'](item.contacts[0].domain_id).domain : '' }}
                  </div>
                </div>
              </template>
              <template v-slot:item.sessions="{ item }">
                <div>
                  <v-btn icon>
                    <v-icon
                      small
                      @click="showSessions(item.id)"
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
      v-model="dialogSessions"
      light
      max-width="800"
    >
      <v-card class="text-center">
        <v-card-title>
          Сессии
          <v-spacer />
          <v-icon aria-label="Close" @click="dialogSessions = false">
            mdi-close
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="headersSessions"
            :items="$store.getters['leads/rootUserSessions']"
            :loading="loadingTable"
            :server-items-length="rootTotalPageSessions"
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
    await this.$store.dispatch('cabinet/getAll')
    await this.$store.dispatch('domain/rootGetAll')
    await this.$store.dispatch('leads/getRootStatistics')
    await this.$store.dispatch('leads/getRootUsers', { perPage: this.options.itemsPerPage, page: this.options.page })
    await this.$store.dispatch('provider/getAll')
    await this.$store.dispatch('leads/getRootProviders')
  },
  data () {
    return {
      currentSessionsUserId: null,
      optionsSessions: { itemsPerPage: 10, page: 1 },
      options: { itemsPerPage: 10, page: 1 },
      providersFromDateMenu: false,
      providersToDateMenu: false,
      dateProvidersFrom: null,
      dateProvidersTo: null,
      clientsFromDateMenu: false,
      clientsToDateMenu: false,
      dateClientsFrom: null,
      dateClientsTo: null,
      clientsCabinet: null,
      clientsDomain: null,
      loadingCsv: null,
      loadingTable: false,
      loadingProvidersTable: false,
      loadingCreate: false,
      loadingDelete: false,
      loadingUpdate: false,
      deleteItem: false,
      dialogCreate: false,
      dialogDelete: false,
      dialogUpdate: false,
      dialogSessions: false,
      updateModel: null,
      createName: null,
      createCode: null,
      createDescription: null,
      updateName: null,
      updateCode: null,
      updateDescription: null,
      updateOn: null,
      updateId: null,
      domainId: null,
      headers: [
        {
          text: 'Контакты',
          value: 'contacts',
          sortable: false,
          align: 'left'
        },
        {
          text: 'Поставщик',
          value: 'providers',
          sortable: false,
          align: 'left'
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
        }
      ],
      headersProviders: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          text: 'Имя',
          value: 'name',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Цена',
          value: 'price',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Запросов',
          value: 'sended',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Контактов',
          value: 'contacts',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Конверсия',
          value: 'conversion',
          sortable: false,
          align: 'right'
        },
        {
          text: 'Прогноз',
          value: 'forecast',
          sortable: false,
          align: 'right'
        }
      ]
    }
  },

  computed: {
    listLeads () {
      return this.$store.getters['leads/rootList']
    },
    rootTotalPage () {
      return this.$store.getters['leads/rootTotalPage']
    },
    rootLastPage () {
      return this.$store.getters['leads/rootLastPage']
    },
    rootTotalPageSessions () {
      return this.$store.getters['leads/rootTotalPageSessions']
    },
    rootLastPageSessions () {
      return this.$store.getters['leads/rootLastPageSessions']
    },
    conversion () {
      return this.$store.getters['leads/rootStatistics'] !== 0 && this.$store.getters['leads/rootStatistics'].users
        ? +(
          (this.$store.getters['leads/rootStatistics'].users * 100 / this.$store.getters['leads/rootStatistics'].sessions).toFixed(2)
        ) + '%'
        : '0%'
    },
    cabinets () {
      return this.$store.getters['cabinet/list']
    }
  },
  watch: {
    options: {
      handler () {
        this.$store.dispatch('leads/getRootUsers', {
          data: { perPage: this.options.itemsPerPage, page: this.options.page }
        })
      },
      deep: true
    },
    optionsSessions: {
      handler () {
        if (this.dialogSessions) {
          this.$store.dispatch('leads/getRootUserSessions', {
            perPage: this.optionsSessions.itemsPerPage,
            page: this.optionsSessions.page,
            id: this.currentSessionsUserId
          })
        }
      },
      deep: true
    }
  },

  methods: {
    selectProvidersEvent () {
      if (this.dateProvidersFrom && this.dateProvidersTo) {
        if (Date.parse(this.dateProvidersFrom) > Date.parse(this.dateProvidersTo)) {
          const from = this.dateProvidersTo
          this.dateProvidersTo = this.dateProvidersFrom
          this.dateProvidersFrom = from
        }
      }
      this.loadingProvidersTable = true
      this.$store.dispatch('leads/getRootProviders', {
        from: this.dateProvidersFrom ? this.dateProvidersFrom + ' 00:00:00' : null,
        to: this.dateProvidersTo ? this.dateProvidersTo + ' 23:59:59' : null
      }).finally(() => {
        this.loadingProvidersTable = false
      })
    },

    selectClientsEvent () {
      if (this.dateClientsFrom && this.dateClientsTo) {
        if (Date.parse(this.dateClientsFrom) > Date.parse(this.dateClientsTo)) {
          const from = this.dateClientsTo
          this.dateClientsTo = this.dateClientsFrom
          this.dateClientsFrom = from
        }
      }
      this.getAllClients()
    },

    getAllClients () {
      this.loadingTable = true
      this.$store.dispatch('leads/getRootUsers', {
        domainId: this.clientsDomain ? this.clientsDomain.id : null,
        cabinetId: this.clientsCabinet ? this.clientsCabinet.id : null,
        from: this.dateClientsFrom ? this.dateClientsFrom + ' 00:00:00' : null,
        to: this.dateClientsTo ? this.dateClientsTo + ' 23:59:59' : null,
        perPage: this.options.itemsPerPage,
        page: this.options.page
      }).finally(() => {
        this.loadingTable = false
      })
    },

    clearClients () {
      this.clientsDomain = null
      this.clientsCabinet = null
      this.dateClientsFrom = null
      this.dateClientsTo = null
      this.getAllClients()
    },

    showSessions (id) {
      this.currentSessionsUserId = id
      this.dialogSessions = true
      this.$store.dispatch('leads/getRootUserSessions', {
        id: this.currentSessionsUserId,
        perPage: this.optionsSessions.itemsPerPage,
        page: this.optionsSessions.page
      })
    },

    toggleProvider (item) {
      this.loadingTable = true
      const toggleData = {
        id: item.id,
        name: item.name,
        code: item.code,
        description: item.description,
        on: !item.on
      }

      this.$store.dispatch('provider/update', toggleData).then(() => {
        this.$store.dispatch('provider/getAll')
        this.$notify.show(this.$t('cabinets.notify.create.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('cabinets.notify.create.error'), 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingTable = false
      })
    },

    getCsv () {
      this.loadingCsv = true
      this.$axios.get('px/v1/admin/users/csv', {
        responseType: 'blob',
        params: {
          domainId: this.clientsDomain ? this.clientsDomain.id : null,
          cabinetId: this.clientsCabinet ? this.clientsCabinet.id : null,
          from: this.dateClientsFrom ? this.dateClientsFrom + ' 00:00:00' : null,
          to: this.dateClientsTo ? this.dateClientsTo + ' 23:59:59' : null
        }
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        const currentTime = new Date()
        link.href = url
        link.setAttribute('download', currentTime.getFullYear() + '-' + (currentTime.getMonth() + 1) + '-' + currentTime.getDate() + '-users.csv')
        document.body.appendChild(link)
        link.click()
        link.remove()
      }).catch(() => {
        this.$notify.show('Произошла ошибка при выгрузке', 'error')
      }).finally(() => {
        this.loadingCsv = false
      })
    },

    selectClientsCabinetEvent () {
      this.getAllClients()
      this.clientsDomain = null
    },

    selectClientsDomainEvent () {
      this.getAllClients()
    }
  }
}
</script>

<style scoped>

</style>
