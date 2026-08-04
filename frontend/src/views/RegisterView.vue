<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

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

    localStorage.setItem('token', response.data.token)
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
  <div class="min-h-screen flex items-center justify-center bg-zinc-950 text-zinc-100">
    <div class="w-full max-w-sm p-8 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl">
      <h2 class="text-3xl font-bold text-center mb-8">Criar Conta</h2>

      <form @submit.prevent="handleRegister" class="space-y-5">
        <div class="flex flex-col space-y-1">
          <label class="text-sm font-medium text-zinc-400">Nome</label>
          <input
            type="text"
            v-model="form.name"
            required
            class="px-4 py-2 bg-zinc-950 border border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          />
        </div>

        <div class="flex flex-col space-y-1">
          <label class="text-sm font-medium text-zinc-400">Username</label>
          <input
            type="text"
            v-model="form.username"
            required
            class="px-4 py-2 bg-zinc-950 border border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          />
        </div>

        <div class="flex flex-col space-y-1">
          <label class="text-sm font-medium text-zinc-400">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="px-4 py-2 bg-zinc-950 border border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          />
        </div>

        <div class="flex flex-col space-y-1">
          <label class="text-sm font-medium text-zinc-400">Senha</label>
          <input
            type="password"
            v-model="form.password"
            required
            minlength="8"
            class="px-4 py-2 bg-zinc-950 border border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          />
        </div>

        <!-- Mensagem de erro elegante -->
        <div v-if="errorMessage" class="p-3 bg-red-500/10 border border-red-500/50 rounded-lg">
          <p class="text-sm text-red-400 text-center">{{ errorMessage }}</p>
        </div>

        <button
          type="submit"
          class="w-full py-2.5 mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition-colors"
        >
          Cadastrar
        </button>
      </form>
    </div>
  </div>
</template>
