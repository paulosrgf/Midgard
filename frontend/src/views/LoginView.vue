<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    await authStore.login({ email: email.value, password: password.value })
    router.push('/')
  } catch (error) {
    errorMessage.value = 'Acesso negado pelos deuses.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-[#0a0a0a] flex items-center justify-center p-4 selection:bg-red-900/40"
  >
    <div
      class="flex flex-col md:flex-row w-full max-w-5xl bg-zinc-950/80 backdrop-blur-xl border border-zinc-800 rounded-lg shadow-2xl shadow-black overflow-hidden"
    >
      <!-- ARTE / LORE (Com os mesmos efeitos do Register) -->
      <div class="hidden md:block md:w-1/2 relative border-r border-zinc-900 group">
        <!-- Mantive a imagem das estátuas, mas com o efeito de ganhar cor no hover -->
        <img
          src="https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=2000&auto=format&fit=crop"
          alt="Valhalla"
          class="absolute inset-0 w-full h-full object-cover grayscale opacity-50 group-hover:opacity-80 group-hover:grayscale-0 transition-all duration-1000"
        />
        <div
          class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/40 to-transparent"
        ></div>

        <div class="relative h-full flex flex-col justify-end p-14">
          <h1 class="font-serif text-4xl text-zinc-100 uppercase tracking-[0.2em] mb-4">Midgard</h1>
          <p
            class="font-sans text-xs text-zinc-400 uppercase tracking-widest leading-relaxed max-w-sm"
          >
            Somente os dignos atravessam os portões. Reconheça-se para adentrar o salão.
          </p>
        </div>
      </div>

      <!-- FORMULÁRIO -->
      <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col justify-center bg-transparent relative">
        <div class="mb-10 border-b border-zinc-900 pb-6">
          <h2 class="text-3xl font-serif text-zinc-100 tracking-widest uppercase">Entrar</h2>
        </div>

        <form @submit.prevent="handleLogin" class="flex flex-col gap-5">
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

          <div class="flex flex-col gap-2">
            <div class="flex justify-between items-center">
              <label class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest"
                >Senha</label
              >
            </div>
            <input
              v-model="password"
              type="password"
              required
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-sm p-4 focus:border-red-700 focus:outline-none transition-colors rounded-none placeholder:text-zinc-800"
              placeholder="••••••••"
            />
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
            {{ isLoading ? 'Abrindo portões...' : 'Atravessar' }}
          </button>
        </form>

        <div class="mt-10 text-center">
          <p class="font-sans text-[10px] text-zinc-500 uppercase tracking-widest">
            Ainda não forjou seu nome?
            <RouterLink
              to="/register"
              class="text-zinc-300 hover:text-red-500 ml-2 transition-colors border-b border-zinc-700 hover:border-red-500 pb-1"
            >
              Forjar Aliança
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
