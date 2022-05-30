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
          <v-card-title class="display-2 font-weight-light">
            Биллинг
            <v-spacer />
          </v-card-title>
        </v-col>
      </template>

      <v-container>
        <v-data-table
          :headers="headers"
          :items="campaigns"
          single-expand
          :expanded.sync="expanded"
          item-key="id"
          show-expand
          :loading="loadingTable"
        >
          <template v-slot:item.bill="{ item }">
            <v-btn text @click="openCreateDialog(item)">
              <v-icon class="ml-2">
                mdi-plus
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:expanded-item="{ headers, item }">
            <td :colspan="headers.length">
              <div
                v-for="(payment, i) in item.payments"
                :key="i"
              >
                <span style="color: darkgreen; margin-right: 20px;">+ {{ payment.arrival }} руб.</span>
                <span>{{ payment.created_at }}</span>
              </div>
            </td>
          </template>
        </v-data-table>
      </v-container>
    </BaseMaterialCard>
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
  </v-container>
</template>

<script>
export default {
  name: 'Billing',

  async fetch () {
    await this.$store.dispatch('campaign/getAll', { cabinetId: this.$route.params.cabinetId, domainId: this.$route.params.domainId })
  },

  data () {
    return {
      headers: [
        {
          sortable: false,
          text: 'ID',
          value: 'id'
        },
        {
          sortable: false,
          text: 'Баланс',
          value: 'balance'
        },
        {
          sortable: false,
          text: 'Скидка',
          value: 'discount'
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
          text: 'Создать счет',
          value: 'bill'
        }
      ],
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
          text: 'Дата создания',
          value: 'created_at'
        }
      ],
      expanded: [],
      loadingTable: false,
      dialogCreateBill: false,
      createBillCampaignId: null,
      createBillArrival: null,
      loadingCreateBill: false
    }
  },

  computed: {
    campaigns () {
      return this.$store.getters['campaign/list']
    }
  },

  methods: {
    openCreateDialog (item) {
      this.dialogCreateBill = true
      this.createBillCampaignId = item.id
    },

    tryCreateBill () {
      this.loadingCreateBill = true
      this.$store.dispatch('billing/store', {
        cabinetId: this.$route.params.cabinetId,
        domainId: this.$route.params.domainId,
        campaignId: this.createBillCampaignId,
        arrival: this.createBillArrival
      }).then(() => {
        this.$store.dispatch('campaign/getAll', { cabinetId: this.$route.params.cabinetId, domainId: this.$route.params.domainId })
        this.$notify.show(this.$t('domains.notify.delete.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('domains.notify.delete.error'), 'error')
      }).finally(() => {
        this.loadingCreateBill = false
        this.dialogCreateBill = false
      })
    }
  }
}
</script>

<style scoped>

</style>
