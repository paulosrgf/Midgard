<script setup>
import { ref, watch } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import MidgardLogo from '../components/MidgardLogo.vue'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const results = ref([])
const isSearching = ref(false)

// Assiste o que o usuário digita e busca na API automaticamente
watch(searchQuery, async (newVal) => {
  if (newVal.length < 2) {
    results.value = []
    return
  }
  
  isSearching.value = true
  try {
    const response = await api.get(`/search?q=${newVal}`)
    results.value = response.data
  } catch (error) {
    console.error('Erro na busca:', error)
  } finally {
    isSearching.value = false
  }
})

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex justify-center relative">
    <div class="w-full max-w-[1200px] flex flex-col md:flex-row justify-between">
      
      <!-- Navegação Lateral Esquerda -->
      <nav class="hidden md:flex flex-col w-[240px] xl:w-[260px] border-r border-zinc-900 p-4 sticky top-0 h-screen shrink-0 justify-between">
        <div>
          <div class="mb-8 px-2 pt-4">
            <MidgardLogo />
          </div>
          <div class="flex flex-col gap-1 text-zinc-200">
            <RouterLink to="/" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Página Inicial</span>
            </RouterLink>

            <!-- Botão Pesquisa (Ativo) -->
            <RouterLink to="/busca" class="group flex items-center gap-4 p-3 rounded-lg bg-zinc-900 text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
              <span class="text-[15px] font-bold">Pesquisa</span>
            </RouterLink>

            <RouterLink to="/perfil" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop" alt="Perfil" class="w-6 h-6 rounded-full object-cover border border-zinc-700"/>
              <span class="text-[15px] font-medium group-hover:text-white">Perfil</span>
            </RouterLink>
          </div>
        </div>
        <button @click="handleLogout" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-red-950/40 text-zinc-200 transition-colors mb-4 w-full text-left">
           <span class="text-[15px] font-medium group-hover:text-red-500 transition-colors">Sair</span>
        </button>
      </nav>

      <!-- Área Principal de Busca -->
      <main class="flex-1 w-full max-w-[630px] mx-auto pb-20 md:pb-8 pt-8 px-4 md:px-0">
        <div class="mb-8 relative">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Pesquisar por nome ou @username..." 
            class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-4 text-zinc-100 focus:outline-none focus:border-indigo-500 transition-colors pl-12"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-4 text-zinc-500" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
        </div>

        <div v-if="isSearching" class="text-center text-zinc-500 py-10">Buscando...</div>
        
        <div v-else-if="results.length > 0" class="flex flex-col gap-4">
          <RouterLink v-for="user in results" :key="user.id" :to="'/u/' + user.username" class="flex items-center gap-4 p-3 bg-zinc-900/50 hover:bg-zinc-900 rounded-xl transition-colors cursor-pointer">
            <img :src="user.avatar" alt="Avatar" class="w-12 h-12 rounded-full object-cover border border-zinc-700" />
            <div class="flex flex-col">
              <span class="font-bold text-zinc-100">{{ user.username }}</span>
              <span class="text-sm text-zinc-500">{{ user.name }}</span>
            </div>
          </RouterLink>
        </div>

        <div v-else-if="searchQuery.length >= 2" class="text-center text-zinc-500 py-10">
          Nenhum resultado encontrado para "{{ searchQuery }}".
        </div>
      </main>
      
      <!-- Coluna fantasma para alinhar o centro no layout -->
      <aside class="hidden lg:block w-[320px] pt-12 pl-8 sticky top-0 h-screen shrink-0"></aside>
    </div>

    <!-- Navegação Inferior (Mobile) -->
    <nav class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-900 flex justify-around p-3 z-50">
      <RouterLink to="/" class="text-zinc-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/></svg></RouterLink>
      
      <!-- Lupa Ativa Mobile -->
      <RouterLink to="/busca" class="text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg></RouterLink>
      
      <RouterLink to="/perfil" class="text-zinc-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg></RouterLink>
    </nav>
  </div>
</template>