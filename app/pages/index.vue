<template>
  <div
    class="black--text landing"
  >
    <v-parallax
      src="https://images.unsplash.com/photo-1590431306482-f700ee050c59?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80"
      style="min-height: 100vh; max-height: 999px;"
    >
      <v-layout column align-center justify-center class="white--text display-1">
        <h1 class="white--text mb-6 text-xs-center" style="font-size: 70px; font-weight: 900; text-shadow: 3px 2px #000000">
          Avodata.io
        </h1>
        <div class="white--text subheading mb-3 text-xs-center" style="margin-top: 40px; font-size: 20px; width: 80%; text-shadow: 2px 2px #000000">
          Облачная платформа, где каждый может создавать и совершенствовать свои цифровые сервисы, используя инфраструктуру и уникальные технологии Яндекса.
        </div>
        <v-btn class="mt-5" style="background: #9db741;" dark large @click="$routeri18n.push('register')">
          Подключиться
        </v-btn>
      </v-layout>
    </v-parallax>
    <section ref="works" class="works">
      <div class="container">
        <h2>Наша работа</h2>
        <p class="desc">
          Инструменты и технологии для создания собственных облачных приложений и управления жизненным циклом разработки ПО. Позволят автоматически разворачивать тестовые окружения, строить отказоустойчивые системы, работать с бессерверными вычислениями и интернетом вещей.
        </p>
        <div class="items">
          <div class="item">
            <v-icon class="ic">
              mdi-plus
            </v-icon>
            <div>
              <div class="title">
                Year-Round Availability
              </div>
              <div class="text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
              </div>
            </div>
          </div>
          <div class="item">
            <v-icon class="ic">
              mdi-plus
            </v-icon>
            <div>
              <div class="title">
                Year-Round Availability
              </div>
              <div class="text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
              </div>
            </div>
          </div>
          <div class="item">
            <v-icon class="ic">
              mdi-plus
            </v-icon>
            <div>
              <div class="title">
                Year-Round Availability
              </div>
              <div class="text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
              </div>
            </div>
          </div>
          <div class="item">
            <v-icon class="ic">
              mdi-plus
            </v-icon>
            <div>
              <div class="title">
                Year-Round Availability
              </div>
              <div class="text">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section ref="tariffs" class="tariffs">
      <div class="container">
        <h2>Тарифы</h2>
        <div class="items">
          <div class="item">
            <div class="title">
              Быстрый старт
            </div>
            <div class="text">
              Доступно при регистрации
            </div>
            <div class="text bolder price">
              Бесплатно
            </div>
            <div class="text">
              30 контактов
            </div>
          </div>
          <div class="item">
            <div class="title">
              Малый бизнес
            </div>
            <div class="text">
              Подходит под начинающий бизнес
            </div>
            <div class="text bolder price">
              5400 Р
            </div>
            <div class="text">
              300 контактов
            </div>
          </div>
          <div class="item">
            <div class="title">
              Средний бизнес
            </div>
            <div class="text">
              Подходит под средний бизнес
            </div>
            <div class="text bolder price">
              15 000 Р
            </div>
            <div class="text">
              1000 контактов
            </div>
          </div>
          <div class="item">
            <div class="title">
              Большой бизнес
            </div>
            <div class="text">
              Подходит под крупный бизнес
            </div>
            <div class="text bolder price">
              36 000 Р
            </div>
            <div class="text">
              3000 контактов
            </div>
          </div>
        </div>
      </div>
    </section>
    <section ref="interface" class="screens">
      <div class="container">
        <h2>Удобные интерфейсы для решения ваших задач</h2>
        <p class="desc">
          Легко подключайте и настраивайте нужные сервисы в консоли управления. Установите интерфейс командной строки (CLI) или управляйте ресурсами через Terraform. В подробной документации найдутся все необходимые пошаговые руководства.
        </p>
        <img src="/interface.png" alt="">
      </div>
    </section>

    <section ref="contacts" class="contacts">
      <div class="container">
        <div class="first">
          <h2>Оставить заявку</h2>
          <p class="desc">
            Инструменты и технологии для создания собственных облачных приложений и управления жизненным циклом разработки ПО. Позволят автоматически разворачивать тестовые окружения, строить отказоустойчивые системы, работать с бессерверными вычислениями и интернетом вещей.
          </p>
          <v-form light @submit.stop.prevent="tryBack()">
            <v-container class="py-0">
              <v-row>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-account</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-text-field
                    v-model="nameClient"
                    label="Имя"
                    class="purple-input"
                  />
                </v-col>
                <v-col cols="2" align-self="center" class="d-flex justify-center">
                  <v-icon>mdi-account</v-icon>
                </v-col>
                <v-col cols="10" class="py-0">
                  <v-text-field
                    v-model="descriptionClient"
                    label="Описание"
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
                    :disabled="nameClient && descriptionClient"
                    :loading="loading"
                  >
                    Оставить заявку
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </div>
        <div class="second">
          <img src="/register.jpg" alt="">
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'Index',

  asyncData (ctx) {
    ctx.redirect(ctx.app.localePath({ name: 'login' }))
  },

  data () {
    return {
      scrollOption: {
        duration: 300,
        offset: 0,
        easing: 'easeInOutCubic'
      },
      loading: false,
      descriptionClient: '',
      nameClient: ''
    }
  },

  mounted () {
    this.$routeri18n.push('login')
  },

  methods: {
    tryBack () {

    }
  }
}
</script>
