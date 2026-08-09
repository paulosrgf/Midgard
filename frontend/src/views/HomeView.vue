<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import MidgardLogo from '../components/MidgardLogo.vue'
import PostCard from '../components/PostCard.vue'
import CreatePostModal from '../components/CreatePostModal.vue'
import StoryViewerModal from '../components/StoryViewerModal.vue' // <-- Componente do Story importado

const router = useRouter()
const authStore = useAuthStore()
const isCreateModalOpen = ref(false)

const posts = ref([])
const stories = ref({}) // O backend agora manda um objeto agrupado por user_id
const selectedStoryGroup = ref(null)

const fetchPosts = async () => {
  try {
    const response = await api.get('/posts')
    posts.value = response.data
  } catch (error) {
    console.error('Erro ao buscar os posts:', error)
  }
}

// Busca os stories reais da API (filtrando as últimas 24h)
const fetchStories = async () => {
  try {
    const response = await api.get('/stories')
    stories.value = response.data
  } catch (error) {
    console.error('Erro ao buscar stories:', error)
  }
}

const handleStoryUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  try {
    const formData = new FormData()
    formData.append('media', file)
    
    // Envia a imagem
    await api.post('/stories', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    // Recarrega a barra de stories para a sua foto aparecer na hora!
    await fetchStories()
  } catch (error) {
    console.error('Erro ao criar story:', error)
    alert('Erro ao postar o story. Verifique se é uma imagem válida.')
  }
}

const handlePostSubmit = async (postData) => {
  try {
    const formData = new FormData()
    formData.append('image', postData.image)
    if (postData.caption) {
      formData.append('caption', postData.caption)
    }

    await api.post('/posts', formData)
    await fetchPosts()
  } catch (error) {
    console.error('Erro ao criar postagem:', error)
    alert('Ocorreu um erro ao publicar.')
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

// Abre o visualizador de tela cheia
const openStory = (userGroup) => {
  selectedStoryGroup.value = userGroup
}

onMounted(() => {
  fetchPosts()
  fetchStories() // Dispara a busca de stories ao abrir a tela
})

const suggestions = ref([
  {
    id: 1,
    username: 'devops_cloud',
    relation: 'Novo no Midgard',
    avatar: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&h=150&fit=crop',
  },
  {
    id: 2,
    username: 'frontend_ninja',
    relation: 'Sugerido para você',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop',
  },
])
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex justify-center relative">
    <div class="w-full max-w-[1200px] flex flex-col md:flex-row justify-between">
      <!-- 1. Navegação Lateral Esquerda (Desktop) -->
      <nav class="hidden md:flex flex-col w-[240px] xl:w-[260px] border-r border-zinc-900 p-4 sticky top-0 h-screen shrink-0 justify-between">
        <div>
          <!-- Logo com padding ajustado -->
          <div class="mb-8 px-2 pt-4">
            <MidgardLogo />
          </div>

          <!-- Links de Navegação -->
          <div class="flex flex-col gap-1 text-zinc-200">
            <RouterLink to="/" class="group flex items-center gap-4 p-3 rounded-lg bg-zinc-900 text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" /></svg>
              <span class="text-[15px] font-bold">Página Inicial</span>
            </RouterLink>

            <!-- Botão de Pesquisa ajustado para apontar para /busca -->
            <RouterLink to="/busca" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:scale-105 transition-transform" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" /></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Pesquisa</span>
            </RouterLink>

            <a href="#" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:scale-105 transition-transform" viewBox="0 0 16 16"><path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" /></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Notificações</span>
            </a>

            <!-- Botão Criar -->
            <a href="#" @click.prevent="isCreateModalOpen = true" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:scale-105 transition-transform" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" /><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" /></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Criar</span>
            </a>

            <!-- Botão Perfil usando RouterLink -->
            <RouterLink to="/perfil" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop" alt="Perfil" class="w-6 h-6 rounded-full object-cover border border-zinc-700 group-hover:scale-105 transition-transform" />
              <span class="text-[15px] font-medium group-hover:text-white">Perfil</span>
            </RouterLink>
          </div>
        </div>

        <!-- Botão de Sair no rodapé da barra lateral -->
        <button @click="handleLogout" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-red-950/40 text-zinc-200 transition-colors mb-4 w-full text-left">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:text-red-500 transition-colors" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" /><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" /></svg>
          <span class="text-[15px] font-medium group-hover:text-red-500 transition-colors">Sair</span>
        </button>
      </nav>

      <!-- 2. Área Principal do Feed (Centro) -->
      <main class="flex-1 flex justify-center w-full max-w-[630px] mx-auto pb-20 md:pb-0">
        <div class="w-full flex flex-col pt-8 px-4 md:px-0">
          
          <!-- Mundos (Stories Dinâmicos) -->
          <div class="flex gap-4 overflow-x-auto pb-4 mb-8 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
           <!-- Botão Criar Novo Story -->
            <label class="flex flex-col items-center gap-1 cursor-pointer shrink-0 group mr-2">
              <div class="w-16 h-16 rounded-full p-[2px] border-2 border-zinc-700 group-hover:border-zinc-500 transition-colors flex items-center justify-center bg-zinc-900 text-zinc-500 group-hover:text-zinc-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
              </div>
              <span class="text-xs text-zinc-300 truncate w-16 text-center">Seu story</span>
              <!-- Input invisível que dispara o upload -->
              <input type="file" accept="image/*" class="hidden" @change="handleStoryUpload" />
            </label>
            <div
              v-for="(userGroup, userId) in stories"
              :key="userId"
              @click="openStory(userGroup)"
              class="flex flex-col items-center gap-1 cursor-pointer shrink-0 group"
            >
              <div class="w-16 h-16 rounded-full p-[2px] bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 group-hover:scale-105 transition-transform">
                <img
                  :src="userGroup[0].user.avatar ? 'http://localhost:8000/storage/' + userGroup[0].user.avatar : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop'"
                  alt="Story"
                  class="w-full h-full rounded-full object-cover border-2 border-zinc-950"
                />
              </div>
              <span class="text-xs text-zinc-300 truncate w-16 text-center">{{ userGroup[0].user.username }}</span>
            </div>

            <div v-if="Object.keys(stories).length === 0" class="text-sm text-zinc-600 italic">
              Nenhum story ativo no momento.
            </div>
          </div>

          <!-- Feed de Posts -->
          <div class="w-full flex flex-col gap-8">
            <PostCard
              v-for="post in posts"
              :key="post.id"
              :post="post"
              @deleted="posts = posts.filter((p) => p.id !== $event)"
            />
          </div>
        </div>
      </main>

      <!-- 3. Coluna Direita (Sugestões) -->
      <aside class="hidden lg:block w-[320px] pt-12 pl-8 sticky top-0 h-screen shrink-0">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3 cursor-pointer">
            <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop" alt="Meu Perfil" class="w-11 h-11 rounded-full object-cover" />
            <div class="flex flex-col">
              <span class="font-bold text-sm text-white">paulosrgf</span>
              <span class="text-sm text-zinc-400">Paulo Sergio</span>
            </div>
          </div>
          <button class="text-xs font-bold text-indigo-400 hover:text-white transition-colors">
            Mudar
          </button>
        </div>

        <div class="flex items-center justify-between mb-4">
          <span class="text-sm font-semibold text-zinc-400">Sugestões para você</span>
          <button class="text-xs font-bold text-white hover:text-zinc-300 transition-colors">
            Ver tudo
          </button>
        </div>

        <div class="flex flex-col gap-4">
          <div v-for="user in suggestions" :key="user.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3 cursor-pointer">
              <img :src="user.avatar" :alt="user.username" class="w-8 h-8 rounded-full object-cover" />
              <div class="flex flex-col">
                <span class="font-semibold text-sm text-white hover:text-zinc-300">{{ user.username }}</span>
                <span class="text-xs text-zinc-500">{{ user.relation }}</span>
              </div>
            </div>
            <button class="text-xs font-bold text-indigo-400 hover:text-white transition-colors">
              Seguir
            </button>
          </div>
        </div>

        <div class="mt-8 text-xs text-zinc-600">
          <p class="mb-4 cursor-pointer hover:underline">
            Sobre • Ajuda • API • Carreiras • Privacidade • Termos
          </p>
          <p>© 2026 MIDGARD FROM PAULO</p>
        </div>
      </aside>
    </div>

    <!-- Navegação Inferior (Mobile) -->
    <nav class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-900 flex justify-around p-3 z-50">
      <RouterLink to="/" class="text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" /></svg>
      </RouterLink>

      <!-- Lupa mobile apontando para /busca -->
      <RouterLink to="/busca" class="text-zinc-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" /></svg>
      </RouterLink>

      <!-- Botão Perfil Mobile usando RouterLink -->
      <RouterLink to="/perfil" class="text-zinc-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" /></svg>
      </RouterLink>
    </nav>

    <!-- Componente do Modal de Criar Post flutuando no fim do DOM -->
    <CreatePostModal
      v-if="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @submit="handlePostSubmit"
    />

    <!-- Componente do Modal de Visualizar Stories -->
    <StoryViewerModal 
      v-if="selectedStoryGroup" 
      :userGroup="selectedStoryGroup" 
      @close="selectedStoryGroup = null" 
    />
  </div>
</template>