<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-row>
      <v-col
        cols="12"
      >
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3 ma-0"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12" />
          </template>
          <v-card-text>
            Используйте в скрипте подключения значения <span style="font-weight: bold">{sid}</span> и <span style="font-weight: bold">{pid}</span> для успешной передачи параметров
          </v-card-text>
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
                Поставщики
                <v-spacer />
                <v-btn text @click="dialogCreate = true">
                  <div>Добавить поставщика</div>
                  <v-icon class="ml-2">
                    mdi-plus
                  </v-icon>
                </v-btn>
              </v-card-title>
            </v-col>
          </template>

          <v-container>
            <v-data-table
              :headers="headers"
              :items="providers"
              :loading="loadingTable"
            >
              <template v-slot:item.on="{ item }">
                <v-switch
                  :readonly="loadingTable"
                  :input-value="item.on"
                  label="On"
                  @change="toggleProvider(item)"
                />
              </template>
              <template v-slot:item.delete="{ item }">
                <v-btn icon>
                  <v-icon
                    small
                    color="error"
                    @click="deleteProvider(item)"
                  >
                    mdi-delete
                  </v-icon>
                </v-btn>
              </template>
              <template v-slot:item.edit="{ item }">
                <v-btn icon>
                  <v-icon
                    small
                    @click="updateProvider(item)"
                  >
                    mdi-pencil
                  </v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-container>
        </BaseMaterialCard>
      </v-col>
    </v-row>
    <v-dialog
      v-model="dialogDelete"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Вы уверены, что хотите удалить этого поставщика?

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogDelete = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-btn
            color="error"
            @click="tryDeleteProvider"
          >
            Удалить
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogCreate"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Создание нового провайдера

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogCreate = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryCreateProvider">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-account</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="createName"
                  :label="'Имя'"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-c-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-textarea
                  v-model="createCode"
                  :label="'Код подключения'"
                  rows="3"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-d-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-textarea
                  v-model="createDescription"
                  :label="'Описание'"
                  rows="3"
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
      max-width="800"
    >
      <v-card class="text-center">
        <v-card-title>
          Изменить провайдера

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogUpdate = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-form @submit.stop.prevent="tryUpdateProvider">
          <v-container class="py-0">
            <v-row>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-account</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateName"
                  :label="'Имя'"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-c-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-textarea
                  v-model="updateCode"
                  :label="'Скрипт'"
                  rows="3"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-d-box</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-textarea
                  v-model="updateDescription"
                  :label="'Описание'"
                  rows="3"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-web</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateIp"
                  :label="'IP'"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-alpha-t</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updateToken"
                  :label="'Токен'"
                  class="purple-input"
                />
              </v-col>
              <v-col cols="2" align-self="center" class="d-flex justify-center">
                <v-icon>mdi-currency-usd</v-icon>
              </v-col>
              <v-col cols="10" class="py-0">
                <v-text-field
                  v-model="updatePrice"
                  :label="'Цена'"
                  type="number"
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
                  :loading="loadingUpdate"
                >
                  {{ $t('update') }}
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
export default {
  name: 'Providers',

  async fetch () {
    await this.$store.dispatch('provider/getAll')
  },
  data () {
    return {
      loadingTable: false,
      loadingCreate: false,
      loadingDelete: false,
      loadingUpdate: false,
      deleteItem: false,
      dialogCreate: false,
      dialogDelete: false,
      dialogUpdate: false,
      updateModel: null,
      createName: null,
      createCode: null,
      createDescription: null,
      updateName: null,
      updateCode: null,
      updateDescription: null,
      updatePrice: null,
      updateOn: null,
      updateId: null,
      updateIp: null,
      updateToken: null,
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
          text: 'Скрипт',
          value: 'code'
        },
        {
          sortable: false,
          text: 'Описание',
          value: 'description',
          align: 'left'
        },
        {
          sortable: false,
          text: 'On',
          value: 'on',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Цена',
          value: 'price',
          align: 'center'
        },
        {
          sortable: false,
          text: 'ip',
          value: 'ip',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Токен',
          value: 'token',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Удалить',
          value: 'delete',
          align: 'center'
        },
        {
          sortable: false,
          text: 'Отредактировать',
          value: 'edit',
          align: 'center'
        }
      ]
    }
  },

  computed: {
    providers () {
      return this.$store.getters['provider/list']
    }
  },

  methods: {
    dataClear () {
      this.createName = null
      this.createCode = null
      this.createDescription = null
      this.deleteItem = null
      this.updateName = null
      this.updateCode = null
      this.updateDescription = null
      this.updatePrice = null
      this.updateOn = null
      this.updateId = null
      this.updateIp = null
      this.updateToken = null
    },

    updateProvider (item) {
      this.updateName = item.name
      this.updateCode = item.code
      this.updateDescription = item.description
      this.updatePrice = item.price
      this.updateOn = item.on
      this.updateId = item.id
      this.updateIp = item.ip
      this.updateToken = item.token
      this.dialogUpdate = true
    },

    tryUpdateProvider () {
      this.loadingUpdate = true
      const updateData = {
        name: this.updateName,
        code: this.updateCode,
        description: this.updateDescription,
        price: this.updatePrice,
        on: this.updateOn,
        ip: this.updateIp,
        token: this.updateToken,
        id: this.updateId
      }
      this.$store.dispatch('provider/update', updateData).then(() => {
        this.$store.dispatch('provider/getAll')
        this.$notify.show('Провайдер успешно обновлен', 'success')
      }).catch(() => {
        this.$notify.show('Ошибка при обновлении провайдера', 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingUpdate = false
        this.dialogUpdate = false
      })
    },

    deleteProvider (item) {
      this.deleteItem = item
      this.dialogDelete = true
    },

    tryDeleteProvider () {
      this.loadingDelete = true
      const deleteData = {
        id: this.deleteItem.id
      }

      this.$store.dispatch('provider/delete', deleteData.id).then(() => {
        this.$store.dispatch('provider/getAll')
        this.$notify.show('Провайдер успешно удален', 'success')
      }).catch(() => {
        this.$notify.show('Ошибка при удалении провайдера', 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingDelete = false
        this.dialogDelete = false
      })
    },

    tryCreateProvider () {
      this.loadingCreate = true
      const createData = {
        name: this.createName,
        code: this.createCode,
        description: this.createDescription
      }

      this.$store.dispatch('provider/create', createData).then(() => {
        this.$store.dispatch('provider/getAll')
        this.$notify.show('Провайдер успешно создан', 'success')
      }).catch(() => {
        this.$notify.show('Ошибка при создании провайдера', 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingCreate = false
        this.dialogCreate = false
      })
    },

    toggleProvider (item) {
      this.loadingTable = true
      const toggleData = {
        id: item.id,
        name: item.name,
        code: item.code,
        price: item.price,
        ip: item.ip,
        token: item.token,
        description: item.description,
        on: !item.on
      }

      this.$store.dispatch('provider/update', toggleData).then(() => {
        this.$store.dispatch('provider/getAll')
        this.$notify.show('Провайдер успешно обновлен', 'success')
      }).catch(() => {
        this.$notify.show('Ошибка при обновлении провайдера', 'error')
      }).finally(() => {
        this.dataClear()
        this.loadingTable = false
      })
    }
  }
}
</script>

<style scoped>

</style>
