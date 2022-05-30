# pixel

## api ошибки
- 422 - ошибки валидации, следует смотреть какое поле проебалось
- 403 - ошибка, у нас нет достаточных прав, запретить ресурс
- 401 - ошибка авторизации, мы не знаем, кто мы, редирект на login
- 500 - ошибка предвиденная приложением или системная

## основные сервисы
- нотификация
- валидация
- мультиязычность

## Build Setup

```bash
# install dependencies
$ npm install

# serve with hot reload at localhost:3000
$ npm run dev

# build for production and launch server
$ npm run build
$ npm run start

# generate static project
$ npm run generate
```
