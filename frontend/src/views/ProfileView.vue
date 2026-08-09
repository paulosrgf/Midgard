<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import MidgardLogo from '../components/MidgardLogo.vue'
import StoryViewerModal from '../components/StoryViewerModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const profile = ref(null)
const loading = ref(true)

// --- ESTADOS DO MODAL DE EDIÇÃO ---
const isEditModalOpen = ref(false)
const editForm = ref({ name: '', username: '', bio: '', avatar: null })
const avatarPreview = ref(null)
const isSubmitting = ref(false)

// --- ESTADO DOS DESTAQUES ---
const selectedHighlightStories = ref(null)
const isCreateHighlightModalOpen = ref(false)
const newHighlightTitle = ref('')
const myAvailableStories = ref([])
const selectedStoriesForHighlight = ref([])

// Buscar dados do perfil
const fetchProfile = async () => {
  try {
    const response = await api.get('/profile')
    profile.value = response.data
  } catch (error) {
    console.error('Erro ao buscar perfil:', error)
  } finally {
    loading.value = false
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

// --- LÓGICA DE CRIAR E ABRIR DESTAQUES ---
const openCreateHighlightModal = async () => {
  try {
    const response = await api.get('/stories')
    // Pega só os stories do próprio usuário logado
    myAvailableStories.value = response.data[authStore.user.id] || []
    isCreateHighlightModalOpen.value = true
  } catch (error) {
    console.error('Erro ao buscar stories para destaque:', error)
  }
}

const submitHighlight = async () => {
  if (!newHighlightTitle.value || selectedStoriesForHighlight.value.length === 0) {
    return alert('Dê um título e selecione pelo menos um story.')
  }

  try {
    await api.post('/highlights', {
      title: newHighlightTitle.value,
      story_ids: selectedStoriesForHighlight.value
    })
    
    // Limpa e fecha o modal
    isCreateHighlightModalOpen.value = false
    newHighlightTitle.value = ''
    selectedStoriesForHighlight.value = []
    
    // Atualiza a tela de perfil para o destaque novo aparecer
    await fetchProfile()
  } catch (error) {
    console.error('Erro ao criar destaque:', error)
  }
}

const openHighlight = async (highlightId) => {
  try {
    const response = await api.get(`/highlights/${highlightId}`)
    const formattedStories = response.data.stories.map(story => ({
      ...story,
      user: profile.value.user
    }))
    
    if (formattedStories.length > 0) {
      selectedHighlightStories.value = formattedStories
    }
  } catch (error) {
    console.error('Erro ao abrir destaque:', error)
  }
}

// --- LÓGICA DE EDIÇÃO DE PERFIL ---
const openEditModal = () => {
  editForm.value.name = profile.value.user.name || ''
  editForm.value.username = profile.value.user.username || ''
  editForm.value.bio = profile.value.user.bio || ''
  editForm.value.avatar = null
  avatarPreview.value = profile.value.user.avatar
  isEditModalOpen.value = true
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    editForm.value.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

const submitProfileEdit = async () => {
  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('name', editForm.value.name)
    formData.append('username', editForm.value.username)
    formData.append('bio', editForm.value.bio)

    if (editForm.value.avatar) {
      formData.append('avatar', editForm.value.avatar)
    }

    await api.post('/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    await fetchProfile()
    isEditModalOpen.value = false
  } catch (error) {
    console.error('Erro ao atualizar perfil:', error)
    alert(error.response?.data?.message || 'Erro ao atualizar o perfil. Tente outro username.')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchProfile()
})
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex justify-center relative">
    <div class="w-full max-w-[1200px] flex flex-col md:flex-row justify-between">
      <!-- 1. Navegação Lateral Esquerda -->
      <nav class="hidden md:flex flex-col w-[240px] xl:w-[260px] border-r border-zinc-900 p-4 sticky top-0 h-screen shrink-0 justify-between">
        <div>
          <div class="mb-8 px-2 pt-4">
            <MidgardLogo />
          </div>

          <div class="flex flex-col gap-1 text-zinc-200">
            <RouterLink to="/" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:scale-105 transition-transform" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" /></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Página Inicial</span>
            </RouterLink>

            <RouterLink to="/busca" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:scale-105 transition-transform" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" /></svg>
              <span class="text-[15px] font-medium group-hover:text-white">Pesquisa</span>
            </RouterLink>

            <RouterLink to="/perfil" class="group flex items-center gap-4 p-3 rounded-lg bg-zinc-900 text-white transition-colors">
              <img :src="profile?.user?.avatar || 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop'" alt="Perfil" class="w-6 h-6 rounded-full object-cover border border-zinc-700" />
              <span class="text-[15px] font-bold">Perfil</span>
            </RouterLink>
          </div>
        </div>

        <button @click="handleLogout" class="group flex items-center gap-4 p-3 rounded-lg hover:bg-red-950/40 text-zinc-200 transition-colors mb-4 w-full text-left">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="group-hover:text-red-500 transition-colors" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" /><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" /></svg>
          <span class="text-[15px] font-medium group-hover:text-red-500 transition-colors">Sair</span>
        </button>
      </nav>

      <!-- 2. Área Principal do Perfil -->
      <main class="flex-1 w-full max-w-[935px] mx-auto pb-20 md:pb-8 pt-8 px-4 md:px-16" v-if="!loading && profile">
        
        <!-- Header do Perfil -->
        <header class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-20 mb-12">
          <!-- Avatar -->
          <div class="shrink-0 relative">
            <img :src="profile.user.avatar" alt="Avatar" class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border border-zinc-800 p-1" />
          </div>

          <div class="flex-1 flex flex-col items-center md:items-start">
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
              <h1 class="text-xl md:text-2xl font-medium text-zinc-100">{{ profile.user.username }}</h1>
              <div class="flex gap-2">
                <button @click="openEditModal" class="bg-zinc-800 hover:bg-zinc-700 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors text-white">Editar Perfil</button>
                <button class="bg-zinc-800 hover:bg-zinc-700 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors text-white">Ver arquivo</button>
              </div>
            </div>

            <div class="hidden md:flex gap-10 text-[15px] mb-5 text-zinc-100">
              <p><span class="font-bold">{{ profile.user.posts_count }}</span> publicações</p>
              <p><span class="font-bold">0</span> seguidores</p>
              <p><span class="font-bold">0</span> seguindo</p>
            </div>

            <!-- Exibindo nome e bio reais -->
            <div class="text-[15px] text-center md:text-left">
              <p class="font-semibold text-zinc-100 mb-0.5">{{ profile.user.name }}</p>
              <p class="text-zinc-400 whitespace-pre-line">{{ profile.user.bio }}</p>
            </div>
          </div>
        </header>

        <!-- FILEIRA DE DESTAQUES E BOTÃO NOVO -->
        <div v-if="profile.highlights" class="flex gap-4 overflow-x-auto pb-6 mb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
          
          <!-- Botão Novo Destaque -->
          <div @click="openCreateHighlightModal" class="flex flex-col items-center gap-2 cursor-pointer shrink-0 group mr-2">
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 border-zinc-700 group-hover:border-zinc-500 transition-colors flex items-center justify-center bg-zinc-900 text-zinc-500 group-hover:text-zinc-300">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="text-xs font-semibold text-zinc-100 w-16 md:w-20 text-center">Novo</span>
          </div>

          <!-- Destaques Criados -->
          <div 
            v-for="highlight in profile.highlights" 
            :key="highlight.id"
            @click="openHighlight(highlight.id)"
            class="flex flex-col items-center gap-2 cursor-pointer shrink-0 group"
          >
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full p-[2px] border border-zinc-700 group-hover:border-zinc-500 transition-colors bg-zinc-900">
              <img 
                :src="'http://localhost:8000/storage/' + highlight.cover" 
                :alt="highlight.title"
                class="w-full h-full rounded-full object-cover border-[3px] border-zinc-950"
              />
            </div>
            <span class="text-xs font-semibold text-zinc-100 truncate w-16 md:w-20 text-center">{{ highlight.title }}</span>
          </div>
        </div>

        <!-- Status Mobile -->
        <div class="flex md:hidden justify-around border-t border-zinc-800 py-3 text-sm text-center text-zinc-100 mb-4">
          <div class="flex flex-col"><span class="font-bold">{{ profile.user.posts_count }}</span><span class="text-zinc-500 text-xs">publicações</span></div>
          <div class="flex flex-col"><span class="font-bold">0</span> <span class="text-zinc-500 text-xs">seguidores</span></div>
          <div class="flex flex-col"><span class="font-bold">0</span> <span class="text-zinc-500 text-xs">seguindo</span></div>
        </div>

        <div class="border-t border-zinc-800 flex justify-center uppercase text-xs font-bold tracking-widest mt-4 md:mt-0">
          <div class="flex gap-12">
            <button class="flex items-center gap-2 py-4 border-t border-white text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 1h-5A1.5 1.5 0 0 0 4 2.5v11A1.5 1.5 0 0 0 5.5 15h5a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 10.5 1M5.5 2h5a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5"/></svg>
              Publicações
            </button>
          </div>
        </div>

        <!-- Grid de Fotos -->
        <div class="grid grid-cols-3 gap-1 md:gap-4 mt-2">
          <div v-for="post in profile.posts" :key="post.id" class="aspect-square relative group cursor-pointer bg-zinc-900 rounded overflow-hidden">
            <img :src="post.image" alt="Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-6 transition-opacity duration-300">
              <span class="flex items-center gap-2 font-bold text-white text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                {{ post.likes_count }}
              </span>
              <span class="flex items-center gap-2 font-bold text-white text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg>
                {{ post.comments_count }}
              </span>
            </div>
          </div>
        </div>

        <div v-if="profile.posts.length === 0" class="flex flex-col items-center justify-center text-zinc-500 py-20">
          <h2 class="text-2xl font-bold text-zinc-200 mb-2">Compartilhe fotos</h2>
        </div>
      </main>

      <div v-else class="flex-1 flex justify-center items-center h-screen text-zinc-500">
        <svg class="animate-spin h-8 w-8 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
      </div>
    </div>

    <!-- Navegação Inferior (Mobile) -->
    <nav class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-900 flex justify-around p-3 z-50">
      <RouterLink to="/" class="text-zinc-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" /></svg></RouterLink>
      <RouterLink to="/busca" class="text-zinc-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" /></svg></RouterLink>
      <RouterLink to="/perfil" class="text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" /></svg></RouterLink>
    </nav>

    <!-- ============================================== -->
    <!-- MODAL DE EDIÇÃO DE PERFIL -->
    <!-- ============================================== -->
    <div v-if="isEditModalOpen" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl">
        <div class="flex justify-between items-center p-4 border-b border-zinc-800">
          <h2 class="text-lg font-bold text-zinc-100">Editar Perfil</h2>
          <button @click="isEditModalOpen = false" class="text-zinc-400 hover:text-white transition-colors"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <form @submit.prevent="submitProfileEdit" class="p-6 flex flex-col gap-5">
          <div class="flex flex-col items-center gap-3">
            <img :src="avatarPreview" alt="Preview" class="w-24 h-24 rounded-full object-cover border border-zinc-700" />
            <label class="cursor-pointer text-indigo-400 font-semibold text-sm hover:text-indigo-300 transition-colors">Alterar foto de perfil<input type="file" accept="image/*" class="hidden" @change="handleAvatarChange" /></label>
          </div>
          <div class="flex flex-col gap-1">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome</label>
            <input v-model="editForm.name" type="text" class="bg-zinc-950 border border-zinc-800 rounded p-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 transition-colors" />
          </div>
          <div class="flex flex-col gap-1">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome de usuário</label>
            <input v-model="editForm.username" type="text" class="bg-zinc-950 border border-zinc-800 rounded p-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 transition-colors" />
          </div>
          <div class="flex flex-col gap-1">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Bio</label>
            <textarea v-model="editForm.bio" rows="3" class="bg-zinc-950 border border-zinc-800 rounded p-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 transition-colors resize-none"></textarea>
          </div>
          <button type="submit" :disabled="isSubmitting" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-lg transition-colors mt-2 disabled:opacity-50">
            {{ isSubmitting ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </form>
      </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL DE CRIAR DESTAQUE -->
    <!-- ============================================== -->
    <div v-if="isCreateHighlightModalOpen" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
      <div class="bg-zinc-900 border border-zinc-800 rounded-xl w-full max-w-md overflow-hidden shadow-2xl flex flex-col max-h-[80vh]">
        <div class="flex justify-between items-center p-4 border-b border-zinc-800">
          <h2 class="text-lg font-bold text-zinc-100">Novo Destaque</h2>
          <button @click="isCreateHighlightModalOpen = false" class="text-zinc-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="p-6 overflow-y-auto">
          <div class="flex flex-col gap-1 mb-6">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Nome do Destaque</label>
            <input v-model="newHighlightTitle" type="text" placeholder="Ex: Viagens" class="bg-zinc-950 border border-zinc-800 rounded p-2.5 text-zinc-100 focus:outline-none focus:border-indigo-500 transition-colors" />
          </div>

          <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2 block">Selecione os Stories Ativos</label>
          
          <div v-if="myAvailableStories.length === 0" class="text-sm text-zinc-500 bg-zinc-950 p-4 rounded text-center border border-zinc-800">
            Você não tem stories ativos nas últimas 24h para destacar.
          </div>

          <div v-else class="grid grid-cols-3 gap-2">
            <label v-for="story in myAvailableStories" :key="story.id" class="cursor-pointer relative group">
              <input type="checkbox" :value="story.id" v-model="selectedStoriesForHighlight" class="hidden" />
              <img :src="'http://localhost:8000/storage/' + story.media_path" class="w-full aspect-[9/16] object-cover rounded opacity-70 group-hover:opacity-100 transition-opacity" :class="{ 'ring-2 ring-indigo-500 opacity-100': selectedStoriesForHighlight.includes(story.id) }" />
              
              <!-- Checkmark de selecionado -->
              <div v-if="selectedStoriesForHighlight.includes(story.id)" class="absolute top-1 right-1 bg-indigo-500 rounded-full p-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </div>
            </label>
          </div>
        </div>

        <div class="p-4 border-t border-zinc-800">
          <button @click="submitHighlight" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-lg transition-colors">
            Adicionar Destaque
          </button>
        </div>
      </div>
    </div>

    <!-- Componente do Modal de Visualizar Destaques -->
    <StoryViewerModal 
      v-if="selectedHighlightStories" 
      :userGroup="selectedHighlightStories" 
      @close="selectedHighlightStories = null" 
    />

  </div>
</template>