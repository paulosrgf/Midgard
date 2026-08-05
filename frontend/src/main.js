import './style.css' // Ou o nome que estiver o seu arquivo de estilo global

import { createApp } from 'vue'
import { createPinia } from 'pinia' // 1. Importe o Pinia
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia()) // 2. Registre o Pinia ANTES do router
app.use(router)

app.mount('#app')
