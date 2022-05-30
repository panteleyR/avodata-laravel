<template>
  <v-container>
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
            Используйте скрипт <span style="font-weight: bold">{{ scriptHeader }}</span>, чтобы подключить сервис к вашему домену
          </v-card-text>
        </BaseMaterialCard>
      </v-col>
      <v-col
        cols="12"
      >
        <BaseMaterialCard
          color="warning"
          class="px-5 py-3 ma-0"
        >
          <template v-slot:customHeading>
            <v-col class="pa-0" cols="12">
              <v-card-title class="pb-0 display-2 font-weight-light">
                <v-btn
                  v-if="!$auth.isAnalytic()"
                  color="primary"
                  dark
                  class="mb-2"
                  @click="dialogIntegration = true"
                >
                  Интеграции
                </v-btn>
              </v-card-title>
            </v-col>
          </template>
          <v-card-text>
            <v-form @submit.stop.prevent="tryUpdateDomain">
              <v-container class="py-0">
                <v-row>
                  <v-col cols="2" sm="1" align-self="center" class="d-flex justify-center">
                    <v-icon>mdi-alpha-n-box</v-icon>
                  </v-col>
                  <v-col cols="10" sm="11" class="py-0">
                    <v-text-field
                      ref="name"
                      v-model="name"
                      :label="$t('name')"
                      :readonly="$auth.isAnalytic()"
                      class="purple-input"
                      :error-messages="nameErrors"
                      @input="$v.name.$touch()"
                      @blur="$v.name.$touch()"
                    />
                  </v-col>
                  <v-col cols="2" sm="1" align-self="center" class="d-flex justify-center">
                    <v-icon>mdi-web</v-icon>
                  </v-col>
                  <v-col cols="10" sm="11" class="py-0">
                    <v-text-field
                      v-model="domain"
                      :label="$t('domain')"
                      readonly
                      class="purple-input"
                      :error-messages="domainErrors"
                      @input="$v.domain.$touch()"
                      @blur="$v.domain.$touch()"
                    />
                  </v-col>

                  <v-col
                    cols="12"
                    align-self="center"
                    class="d-flex"
                  >
                    <v-switch
                      v-if="current_campaign === null"
                      :disabled="true"
                      :input-value="current_campaign === null ? false : on"
                      class="ma-2"
                      label="On"
                    />
                    <v-switch
                      v-else
                      v-model="on"
                      :readonly="$auth.isAnalytic()"
                      class="ma-2"
                      label="On"
                    />
                  </v-col>
                  <v-col
                    cols="12"
                    align-self="center"
                    class="d-flex"
                  >
                    <v-switch v-model="geoOn" :readonly="$auth.isAnalytic()" class="ma-2" label="Гео-фильтр" />
                  </v-col>

                  <v-col
                    v-if="geoOn"
                    cols="12"
                    align-self="center"
                    class="d-flex"
                  >
                    <v-autocomplete
                      v-model="citiesValues"
                      :items="citiesItems"
                      :search-input.sync="search"
                      dense
                      chips
                      small-chips
                      label="Города"
                      :loading="isSearchLoading"
                      multiple
                      solo
                      item-text="name"
                      item-value="id"
                      return-object
                      hide-no-data
                      :readonly="$auth.isAnalytic()"
                    />
                  </v-col>

                  <v-col
                    cols="12"
                    class="text-right"
                  >
                    <v-btn
                      color="primary"
                      class="mr-2"
                      @click="$routeri18n.push({ name: 'cabinets-cabinetid-domains', params: { cabinetid: $route.params.cabinetid } })"
                    >
                      {{ $t('back') }}
                    </v-btn>
                    <v-btn
                      v-if="!$auth.isAnalytic()"
                      color="primary"
                      class="mr-0"
                      type="submit"
                      :loading="loadingUpdate"
                      :disabled="this.$v.name.$invalid || this.$v.token.$invalid"
                    >
                      {{ $t('update') }}
                    </v-btn>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-card-text>
        </BaseMaterialCard>
      </v-col>
    </v-row>
    <v-dialog
      v-model="dialogIntegration"
      light
      max-width="500"
    >
      <v-card class="text-center">
        <v-card-title>
          Интеграции

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="dialogIntegration = false"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-container class="py-0">
          <v-row>
            <v-col cols="2" align-self="center" class="d-flex justify-center">
              <v-icon>mdi-contain-start</v-icon>
            </v-col>
            <v-col cols="10" class="py-0">
              <v-textarea
                v-model="jsBefore"
                :label="'Скрипт перед пикселем'"
                :readonly="$auth.isAnalytic()"
                rows="3"
                class="purple-input"
              />
            </v-col>

            <v-col cols="2" align-self="center" class="d-flex justify-center">
              <v-icon>mdi-contain-end</v-icon>
            </v-col>
            <v-col cols="10" class="py-0">
              <v-textarea
                v-model="jsAfter"
                :label="'Скрипт после пикселя'"
                :readonly="$auth.isAnalytic()"
                rows="3"
                class="purple-input"
              />
            </v-col>

            <v-col cols="2" align-self="center" class="d-flex justify-center">
              <v-icon>mdi-send-clock</v-icon>
            </v-col>
            <v-col cols="10" class="py-0">
              <v-text-field
                v-model="apiUrl"
                :label="'api_url'"
                :readonly="$auth.isAnalytic()"
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
                @click="dialogIntegration = false"
              >
                ok
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'Id',

  async fetch () {
    await this.$store.dispatch('geo/getAll')
    await this.$store.dispatch('domain/show', { cabinetid: this.$route.params.cabinetid, id: this.$route.params.id }).then((res) => {
      this.domain = res.domain
      this.name = res.name
      this.token = res.token
      this.apiUrl = res.api_url
      this.citiesValues = res.geo
      this.on = res.on
      this.current_campaign = res.current_campaign
      this.geoOn = res.geo_on
      this.id = res.id
      this.jsBefore = res.js_before
      this.jsAfter = res.js_after
    })
  },

  data () {
    return {
      loadingUpdate: false,
      domain: '',
      token: '',
      name: '',
      jsBefore: '',
      jsAfter: '',
      apiUrl: '',
      on: false,
      current_campaign: null,
      geoOn: false,
      citiesValues: [],
      id: null,
      dialogIntegration: false,
      search: null,
      isSearchLoading: false
    }
  },

  computed: {
    scriptHeader () {
      return '<script src="' + process.env.baseUrl + 'px/pixel.js?token=' + this.token + '">' + '<' + '/script>'
    },

    citiesItems () {
      return Array.from(new Set(this.$store.getters['geo/list'].concat(this.citiesValues)))
    },

    domainErrors () {
      const errors = []
      if (!this.$v.domain.$dirty) {
        return errors
      }

      !this.$v.domain.required && errors.push(this.$t('auth.validate.require'))
      return errors
    },

    tokenErrors () {
      const errors = []
      if (!this.$v.token.$dirty) {
        return errors
      }

      !this.$v.token.required && errors.push(this.$t('auth.validate.require'))
      return errors
    },

    nameErrors () {
      const errors = []
      if (!this.$v.name.$dirty) {
        return errors
      }

      !this.$v.name.required && errors.push(this.$t('auth.validate.require'))
      return errors
    }

    // jsBeforeErrors () {
    //   const errors = []
    //   if (!this.$v.jsBefore.$dirty) {
    //     return errors
    //   }
    //
    //   !this.$v.jsBefore.required && errors.push(this.$t('auth.validate.require'))
    //   return errors
    // },
    //
    // jsAfterErrors () {
    //   const errors = []
    //   if (!this.$v.jsAfter.$dirty) {
    //     return errors
    //   }
    //
    //   !this.$v.jsAfter.required && errors.push(this.$t('auth.validate.require'))
    //   return errors
    // }
  },

  validations: {
    domain: {
      required
    },
    name: {
      required
    },
    token: {
      required
    },
    on: {
      required
    }
    // jsBefore: {
    //   required
    // },
    // jsAfter: {
    //   required
    // }
  },

  watch: {
    search (val) {
      // if (this.isSearchLoading) {
      //   return
      // }

      if (this.citiesItems.length === 0) {
        return
      }
      this.isSearchLoading = true
      this.fetchEntriesDebounced(val)
    }
  },

  methods: {
    fetchEntriesDebounced (val) {
      // cancel pending call
      clearTimeout(this._timerId)

      // delay new call 500ms
      this._timerId = setTimeout(() => {
        if (val !== null) {
          this.$store.dispatch('geo/getSearch', val).finally(() => {
            this.isSearchLoading = false
          })
        } else {
          this.$store.dispatch('geo/getAll').finally(() => {
            this.isSearchLoading = false
          })
        }
      }, 700)
    },

    setDomainData (res) {
      this.domain = res.domain
      this.name = res.name
      this.on = res.on
      this.current_campaign = res.current_campaign
      this.geoOn = res.geo_on
      this.citiesValues = res.geo
      this.token = res.token
      this.apiUrl = res.api_url
      this.id = res.id
      this.jsBefore = res.js_before
      this.jsAfter = res.js_after
    },

    tryUpdateDomain () {
      this.loadingUpdate = true
      this.$store.dispatch('domain/update', {
        id: this.id,
        cabinetid: this.$route.params.cabinetid,
        data: {
          domain: this.domain,
          name: this.name,
          jsBefore: this.jsBefore,
          jsAfter: this.jsAfter,
          api_url: this.apiUrl,
          token: this.token,
          geo_cities: this.citiesValues.map((el) => {
            if (typeof el === 'object') {
              return el.id
            }
            return el
          }),
          geo_on: this.geoOn,
          on: this.on
        }
      }).then(() => {
        this.$store.dispatch('domain/show', { cabinetid: this.$route.params.cabinetid, id: this.$route.params.id }).then((res) => {
          this.setDomainData(res)
        })
        this.$notify.show(this.$t('domains.notify.update.success'), 'success')
      }).catch(() => {
        this.$notify.show(this.$t('domains.notify.update.error'), 'error')
      }).finally(() => {
        this.loadingUpdate = false
      })
    }
  }
}
</script>

<style scoped>

</style>
