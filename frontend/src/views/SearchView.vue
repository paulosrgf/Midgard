<script setup>
import { ref } from 'vue'
import api from '../services/api'
import { useRouter } from 'vue-router'

const query = ref('')
const users = ref([])
const router = useRouter()

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const searchUsers = async () => {
  if (query.value.trim().length < 2) return
  try {
    const response = await api.get(`/users/search?q=${query.value}`)
    users.value = response.data
  } catch (error) {
    console.error('Falha na busca:', error)
  }
}

const goToUser = (id) => {
  // Correção: A rota correta é /u/ e não /user/
  router.push(`/u/${id}`)
}
</script>

<template>
  <main
    class="flex-1 flex flex-col items-center w-full max-w-[720px] mx-auto pb-24 md:pb-0 border-r border-zinc-900 bg-black"
  >
    <header
      class="md:hidden sticky top-0 z-40 bg-black/90 backdrop-blur-md border-b border-zinc-900 p-5 flex items-center justify-center w-full"
    >
      <span class="font-serif text-xl font-bold tracking-[0.2em] uppercase text-zinc-100"
        >Explorar</span
      >
    </header>

    <div class="w-full pt-6 md:pt-10 px-8 border-b border-zinc-900 pb-10">
      <h1 class="hidden md:block font-serif text-2xl uppercase tracking-widest text-zinc-100 mb-6">
        Buscar Guerreiros
      </h1>
      <div class="relative">
        <input
          v-model="query"
          @keyup.enter="searchUsers"
          type="text"
          placeholder="NOME DE GUERRA..."
          class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 font-sans text-sm p-5 pl-14 focus:border-red-700 focus:outline-none transition-colors rounded-none placeholder:text-zinc-700 uppercase tracking-widest"
        />
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 w-5 h-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="square"
            stroke-linejoin="miter"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
      </div>
    </div>

    <div class="w-full p-4 flex flex-col gap-2">
      <div
        v-if="users.length === 0 && query.length > 2"
        class="p-10 text-center text-zinc-600 font-sans text-xs uppercase tracking-widest"
      >
        Nenhuma alma encontrada com este nome.
      </div>

      <div
        v-for="user in users"
        :key="user.id"
        @click="goToUser(user.id)"
        class="flex items-center gap-4 p-4 border border-zinc-900 hover:border-zinc-700 bg-zinc-950/50 cursor-pointer transition-colors group"
      >
        <!-- Correção: Verificando se a imagem já vem com o link do Supabase -->
        <img
          :src="
            user.avatar
              ? user.avatar.startsWith('http')
                ? user.avatar
                : storageBaseUrl + user.avatar
              : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&fit=crop'
          "
          class="w-12 h-12 grayscale object-cover group-hover:grayscale-0 transition-all rounded-full"
        />
        <div>
          <span
            class="font-serif text-lg tracking-widest uppercase text-zinc-200 block group-hover:text-red-500 transition-colors"
            >{{ user.username }}</span
          >
        </div>
      </div>
    </div>
  </main>
</template>
