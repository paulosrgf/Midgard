<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import bgImage from '../assets/midgard-bg.jpg'

const router = useRouter()
const form = ref({
  email: '',
  password: '',
})
const errorMessage = ref('')

const handleLogin = async () => {
  try {
    errorMessage.value = ''
    const response = await api.post('/login', form.value)
    localStorage.setItem('token', response.data.access_token)
    router.push('/')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessage.value = 'Credenciais inválidas.'
    } else {
      errorMessage.value = 'Erro ao conectar com o servidor.'
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

      <!-- Lado Direito: Formulário de Login -->
      <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">
        <div class="flex flex-col items-center">
          <h1 class="text-zinc-100 text-5xl mb-8 font-serif italic tracking-wider">Midgard</h1>

          <form @submit.prevent="handleLogin" class="w-full flex flex-col gap-4">
            <div>
              <input
                type="email"
                v-model="form.email"
                required
                placeholder="Email"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div>
              <input
                type="password"
                v-model="form.password"
                required
                placeholder="Senha"
                class="w-full bg-zinc-950/50 border border-zinc-700 text-zinc-200 text-sm p-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all placeholder-zinc-500"
              />
            </div>

            <div
              v-if="errorMessage"
              class="text-red-400 text-sm text-center bg-red-950/30 p-2 rounded border border-red-900"
            >
              {{ errorMessage }}
            </div>

            <button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm py-3 rounded-lg mt-2 transition-colors shadow-lg shadow-indigo-900/20"
            >
              Entrar
            </button>
          </form>

          <a href="#" class="text-xs text-zinc-400 hover:text-zinc-200 mt-6 transition-colors"
            >Esqueceu a senha?</a
          >
        </div>

        <div class="mt-8 pt-6 border-t border-zinc-800 text-center">
          <p class="text-sm text-zinc-400">
            Não tem uma conta?
            <RouterLink
              to="/register"
              class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors"
            >
              Cadastre-se
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
