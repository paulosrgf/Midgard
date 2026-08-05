<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import bgImage from '../assets/midgard-bg.jpg'
import { useAuthStore } from '../stores/auth' // 1. Importe a store

const router = useRouter()
const authStore = useAuthStore() // 2. Inicialize a store

const form = ref({
  name: '',
  username: '',
  email: '',
  password: '',
})

const errorMessage = ref('')

const handleRegister = async () => {
  try {
    errorMessage.value = ''
    const response = await api.post('/register', form.value)

    // 3. SUBSTITUA o localStorage por isso aqui:
    authStore.setToken(response.data.access_token)

    router.push('/')
  } catch (error) {
    if (error.response && error.response.data.errors) {
      const firstErrorKey = Object.keys(error.response.data.errors)[0]
      errorMessage.value = error.response.data.errors[firstErrorKey][0]
    } else {
      errorMessage.value = 'Ocorreu um erro ao tentar criar a conta.'
    }
  }
}
</script>

<template>
  <div class="min-h-screen bg-zinc-950 flex items-center justify-center p-4">
    <div
      class="flex w-full max-w-[850px] bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden border border-zinc-800"
    >
      <!-- Lado Esquerdo: Imagem Local (Oculta no mobile) -->
      <div class="hidden md:block w-1/2">
        <img :src="bgImage" alt="Midgard Atmosphere" class="w-full h-full object-cover" />
      </div>

      <!-- Lado Direito: Formulário de Registro -->
      <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
        <div class="flex flex-col items-center">
          <h1 class="text-zinc-100 text-4xl mb-6 font-serif italic tracking-wider">Midgard</h1>
          <p class="text-zinc-400 text-sm mb-6 text-center">
            Crie sua conta para ver os mundos de seus amigos.
          </p>

          <form @submit.prevent="handleRegister" class="w-full flex flex-col gap-3">
            <div>
              <input
                type="text"
                v-model="form.name"
                required
                placeholder="Nome completo"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-2.5 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div>
              <input
                type="text"
                v-model="form.username"
                required
                placeholder="Nome de usuário"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-2.5 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div>
              <input
                type="email"
                v-model="form.email"
                required
                placeholder="Email"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-2.5 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div>
              <input
                type="password"
                v-model="form.password"
                required
                minlength="8"
                placeholder="Senha"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-2.5 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div
              v-if="errorMessage"
              class="text-red-400 text-sm text-center bg-red-950/30 p-2 rounded border border-red-900 mt-1"
            >
              {{ errorMessage }}
            </div>

            <button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm py-2.5 rounded-lg mt-2 transition-colors shadow-lg shadow-indigo-900/20"
            >
              Cadastrar
            </button>
          </form>
        </div>

        <div class="mt-6 pt-5 border-t border-zinc-800 text-center">
          <p class="text-sm text-zinc-400">
            Já tem uma conta?
            <RouterLink
              to="/login"
              class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors"
            >
              Conecte-se
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
