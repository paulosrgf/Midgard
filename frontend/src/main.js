import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

// Importe APENAS o arquivo onde você configurou o Tailwind
import './style.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
