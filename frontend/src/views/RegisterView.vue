<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()

const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'As runas não coincidem.'
    return
  }
  isLoading.value = true
  errorMessage.value = ''
  try {
    await api.post('/register', {
      name: username.value,
      username: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    await authStore.login({ email: email.value, password: password.value })
    router.push('/')
  } catch (error) {
    errorMessage.value = 'Nome já esculpido nas pedras ou dados inválidos.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-black flex items-center justify-center p-4 selection:bg-red-900/40"
  >
    <div
      class="flex flex-col md:flex-row-reverse w-full max-w-5xl bg-zinc-950 border border-zinc-900 shadow-2xl shadow-black overflow-hidden"
    >
      <!-- ARTE -->
      <div class="hidden md:block md:w-1/2 relative border-l border-zinc-900 group">
        <img
          src="https://images.unsplash.com/photo-1533230687158-95f70a7b4f53?q=80&w=2072&auto=format&fit=crop"
          alt="Runas/Floresta"
          class="absolute inset-0 w-full h-full object-cover grayscale mix-blend-luminosity opacity-40 group-hover:opacity-70 transition-all duration-1000"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
        <div class="relative h-full flex flex-col justify-end p-14 text-right items-end">
          <h1 class="font-serif text-4xl text-zinc-100 uppercase tracking-[0.2em] mb-4">Midgard</h1>
          <p
            class="font-sans text-xs text-zinc-400 uppercase tracking-widest leading-relaxed max-w-sm"
          >
            Grave seu nome na história. Uma vez feito, os corvos espalharão a notícia.
          </p>
        </div>
      </div>

      <!-- FORMULÁRIO -->
      <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col justify-center bg-black relative">
        <div class="mb-10 border-b border-zinc-900 pb-6">
          <h2 class="text-3xl font-serif text-zinc-100 tracking-widest uppercase">
            Forjar Aliança
          </h2>
        </div>

        <form @submit.prevent="handleRegister" class="flex flex-col gap-5">
          <div class="flex flex-col gap-2">
            <label class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest"
              >Nome de Guerra</label
            >
            <input
              v-model="username"
              type="text"
              required
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-sm p-4 focus:border-red-700 focus:outline-none transition-colors rounded-none placeholder:text-zinc-800"
              placeholder="ragnar_dev"
            />
          </div>

          <div class="flex flex-col gap-2">
            <label class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest"
              >E-mail</label
            >
            <input
              v-model="email"
              type="email"
              required
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-sm p-4 focus:border-red-700 focus:outline-none transition-colors rounded-none placeholder:text-zinc-800"
              placeholder="guerreiro@midgard.com"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="flex flex-col gap-2">
              <label class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest"
                >Senha</label
              >
              <input
                v-model="password"
                type="password"
                required
                class="w-full bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-sm p-4 focus:border-red-700 focus:outline-none transition-colors rounded-none"
              />
            </div>
            <div class="flex flex-col gap-2">
              <label class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest"
                >Confirmar Senha</label
              >
              <input
                v-model="passwordConfirmation"
                type="password"
                required
                class="w-full bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-sm p-4 focus:border-red-700 focus:outline-none transition-colors rounded-none"
              />
            </div>
          </div>

          <div v-if="errorMessage" class="bg-red-950/20 border border-red-900/50 p-4 mt-2">
            <p class="font-sans text-xs text-red-500 uppercase tracking-wider text-center">
              {{ errorMessage }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="mt-6 w-full bg-zinc-100 hover:bg-red-700 hover:text-white disabled:bg-zinc-900 text-black font-sans text-xs font-bold uppercase tracking-[0.2em] p-5 transition-all flex justify-center items-center"
          >
            {{ isLoading ? 'Gravando Runas...' : 'Confirmar Juramento' }}
          </button>
        </form>

        <div class="mt-10 text-center">
          <p class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest">
            Já pertence a Midgard?
            <RouterLink
              to="/login"
              class="text-zinc-300 hover:text-red-500 ml-2 transition-colors border-b border-zinc-700 hover:border-red-500 pb-1"
            >
              Entrar no Salão
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
