<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import PostCard from '../components/PostCard.vue'
import CreatePostModal from '../components/CreatePostModal.vue'
import StoryViewerModal from '../components/StoryViewerModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const isCreateModalOpen = ref(false)

const posts = ref([])
const mundos = ref({})
const selectedMundoGroup = ref(null)

const apiUrl = import.meta.env.VITE_API_BASE_URL || ''
const storageBaseUrl = apiUrl
  ? apiUrl.replace('/api', '/storage/')
  : 'http://localhost:8000/storage/'

const fetchPosts = async () => {
  try {
    const response = await api.get('/posts')
    posts.value = response.data
  } catch (error) {
    console.error('Erro ao buscar as sagas:', error)
  }
}

const fetchMundos = async () => {
  try {
    const response = await api.get('/stories')
    mundos.value = response.data
  } catch (error) {
    console.error('Erro ao buscar mundos:', error)
  }
}

const handleMundoUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  try {
    const formData = new FormData()
    formData.append('media', file)

    await api.post('/stories', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    await fetchMundos()
  } catch (error) {
    console.error('Erro ao forjar mundo:', error)
    alert('Os deuses rejeitaram sua oferenda. Tente outra imagem.')
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
    isCreateModalOpen.value = false
  } catch (error) {
    console.error('Erro ao criar postagem:', error)
    alert('Ocorreu um erro ao registrar a saga.')
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

const openMundo = (userGroup) => {
  selectedMundoGroup.value = userGroup
}

onMounted(() => {
  fetchPosts()
  fetchMundos()
})
</script>

<template>
  <!-- WRAPPER GLOBAL (FUNDO PRETO ABSOLUTO) -->
  <div
    class="min-h-screen w-full bg-black text-zinc-100 flex justify-center relative font-sans selection:bg-red-900/40"
  >
    <div class="w-full max-w-[1280px] flex flex-col md:flex-row justify-between">
      <!-- 1. BARRA ESQUERDA (DESKTOP) -->
      <nav
        class="hidden md:flex flex-col w-[260px] border-r border-zinc-900 p-6 sticky top-0 h-screen shrink-0 justify-between bg-black z-10"
      >
        <div>
          <!-- Logo Viking Estilizada -->
          <div class="mb-12 pt-2">
            <h1 class="font-serif text-4xl tracking-[0.2em] uppercase text-zinc-100">Midgard</h1>
          </div>

          <div class="flex flex-col gap-2">
            <!-- Home -->
            <RouterLink
              to="/"
              class="group flex items-center gap-4 p-3 border border-transparent hover:border-zinc-900 hover:bg-zinc-950 transition-all"
            >
              <span
                class="w-1.5 h-1.5 bg-red-700 rounded-none shadow-[0_0_8px_rgba(185,28,28,0.6)]"
              ></span>
              <span class="font-sans text-xs font-bold tracking-widest uppercase text-white"
                >O Salão</span
              >
            </RouterLink>

            <!-- Search -->
            <RouterLink
              to="/busca"
              class="group flex items-center gap-4 p-3 border border-transparent hover:border-zinc-900 hover:bg-zinc-950 transition-all"
            >
              <span
                class="w-1.5 h-1.5 bg-zinc-800 group-hover:bg-red-700 rounded-none transition-colors"
              ></span>
              <span
                class="font-sans text-xs font-bold tracking-widest uppercase text-zinc-500 group-hover:text-white transition-colors"
                >Explorar</span
              >
            </RouterLink>

            <!-- Criar Post -->
            <a
              href="#"
              @click.prevent="isCreateModalOpen = true"
              class="group flex items-center gap-4 p-3 border border-transparent hover:border-zinc-900 hover:bg-zinc-950 transition-all"
            >
              <span
                class="w-1.5 h-1.5 bg-zinc-800 group-hover:bg-red-700 rounded-none transition-colors"
              ></span>
              <span
                class="font-sans text-xs font-bold tracking-widest uppercase text-zinc-500 group-hover:text-white transition-colors"
                >Forjar Saga</span
              >
            </a>

            <!-- Perfil -->
            <RouterLink
              to="/perfil"
              class="group flex items-center gap-4 p-3 border border-transparent hover:border-zinc-900 hover:bg-zinc-950 transition-all mt-4"
            >
              <div class="w-6 h-6 border border-zinc-800 overflow-hidden">
                <img
                  :src="
                    authStore.user?.avatar
                      ? storageBaseUrl + authStore.user.avatar
                      : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&fit=crop'
                  "
                  class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                />
              </div>
              <span
                class="font-sans text-xs font-bold tracking-widest uppercase text-zinc-500 group-hover:text-white transition-colors"
                >Seu Perfil</span
              >
            </RouterLink>
          </div>
        </div>

        <button
          @click="handleLogout"
          class="group flex items-center gap-4 p-3 border border-transparent hover:border-red-900/50 hover:bg-red-950/20 transition-all text-left"
        >
          <span
            class="w-1.5 h-1.5 bg-zinc-800 group-hover:bg-red-600 rounded-none transition-colors"
          ></span>
          <span
            class="font-sans text-xs font-bold tracking-widest uppercase text-zinc-600 group-hover:text-red-500 transition-colors"
            >Partir</span
          >
        </button>
      </nav>

      <!-- 2. FLUXO PRINCIPAL (CENTRO) -->
      <main
        class="flex-1 flex justify-center w-full max-w-[720px] mx-auto pb-24 md:pb-0 border-r border-zinc-900 bg-black"
      >
        <!-- Top Bar (Mobile) -->
        <header
          class="md:hidden sticky top-0 z-40 bg-black/90 backdrop-blur-md border-b border-zinc-900 p-5 flex items-center justify-center w-full"
        >
          <span class="font-serif text-xl font-bold tracking-[0.2em] uppercase text-zinc-100"
            >Midgard</span
          >
        </header>

        <div class="w-full flex flex-col pt-6 md:pt-10 px-4 md:px-8">
          <!-- SEÇÃO MUNDOS -->
          <div class="mb-4 flex items-center justify-between border-b border-zinc-900 pb-2">
            <h2 class="font-serif text-lg text-zinc-100 tracking-widest uppercase">Mundos</h2>
          </div>

          <div class="flex gap-4 overflow-x-auto pb-6 mb-6 snap-x scrollbar-hide">
            <!-- Forjar Mundo -->
            <label
              class="snap-start flex flex-col justify-end w-[96px] h-[140px] bg-zinc-950 border border-zinc-800 cursor-pointer shrink-0 group relative overflow-hidden transition-all hover:border-zinc-400"
            >
              <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent z-10"></div>
              <div
                class="absolute inset-0 flex items-center justify-center z-0 opacity-40 group-hover:opacity-100 transition-opacity text-zinc-500 group-hover:text-red-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="32"
                  height="32"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="square" stroke-linejoin="miter" d="M12 4v16m8-8H4" />
                </svg>
              </div>
              <div
                class="relative z-20 p-2 border-t border-zinc-900 bg-black/80 backdrop-blur-sm text-center"
              >
                <span class="font-serif text-[10px] text-zinc-300 tracking-widest uppercase block"
                  >Forjar</span
                >
              </div>
              <input type="file" accept="image/*" class="hidden" @change="handleMundoUpload" />
            </label>

            <!-- Mundos Ativos -->
            <div
              v-for="(userGroup, userId) in mundos"
              :key="userId"
              @click="openMundo(userGroup)"
              class="snap-start flex flex-col justify-end w-[96px] h-[140px] bg-zinc-900 border border-zinc-800 cursor-pointer shrink-0 group relative overflow-hidden transition-all hover:border-red-700"
            >
              <img
                :src="
                  userGroup[0].user.avatar
                    ? storageBaseUrl + userGroup[0].user.avatar
                    : 'https://images.unsplash.com/photo-1599839619722-39751411ea63?w=300&fit=crop'
                "
                class="absolute inset-0 w-full h-full object-cover grayscale mix-blend-luminosity group-hover:grayscale-0 group-hover:mix-blend-normal transition-all duration-500"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-10"
              ></div>
              <div
                class="relative z-20 p-2 text-center border-t border-red-900/30 bg-black/60 backdrop-blur-sm"
              >
                <span
                  class="font-serif text-[10px] text-zinc-100 tracking-widest uppercase block truncate"
                  >{{ userGroup[0].user.username }}</span
                >
              </div>
            </div>

            <div
              v-if="Object.keys(mundos).length === 0"
              class="flex items-center justify-center w-full border border-dashed border-zinc-800 h-[140px] bg-zinc-950"
            >
              <span class="font-serif text-xs text-zinc-600 uppercase tracking-widest"
                >Nenhum mundo ativo</span
              >
            </div>
          </div>

          <!-- SEÇÃO FEED (SAGAS) -->
          <div class="mb-6 flex items-center justify-between border-b border-zinc-900 pb-2">
            <h2 class="font-serif text-lg text-zinc-100 tracking-widest uppercase">Sagas</h2>
          </div>

          <!-- Empty State -->
          <div
            v-if="posts?.length === 0"
            class="w-full flex flex-col items-center text-center border border-zinc-900 p-12 bg-zinc-950 mt-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="48"
              height="48"
              fill="none"
              stroke="currentColor"
              stroke-width="1"
              class="text-zinc-700 mb-6"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="square"
                stroke-linejoin="miter"
                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
              />
            </svg>
            <h2 class="text-xl font-serif tracking-widest uppercase text-zinc-300">
              O Salão está vazio
            </h2>
            <p class="text-sm text-zinc-500 mt-3 mb-8 max-w-sm">
              Nenhuma saga foi contada ainda. Siga guerreiros ou forje sua própria história.
            </p>
          </div>

          <div v-else class="w-full flex flex-col gap-10 pb-10">
            <PostCard
              v-for="post in posts"
              :key="post.id"
              :post="post"
              @deleted="posts = posts.filter((p) => p.id !== $event)"
              class="border-zinc-900 rounded-none bg-black"
            />
          </div>
        </div>
      </main>

      <!-- 3. BARRA DIREITA (SUGESTÕES / PERFIL LOCAL) -->
      <!-- Removi todos os usuários fakes! Agora só exibe info real e rodapé. -->
      <aside
        class="hidden lg:flex flex-col w-[340px] pt-10 px-8 sticky top-0 h-screen shrink-0 bg-black"
      >
        <div class="flex items-center justify-between pb-6 mb-8 border-b border-zinc-900">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 border border-zinc-800 p-0.5 bg-zinc-950">
              <img
                :src="
                  authStore.user?.avatar
                    ? storageBaseUrl + authStore.user.avatar
                    : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&fit=crop'
                "
                alt="Perfil"
                class="w-full h-full object-cover grayscale"
              />
            </div>
            <div class="flex flex-col">
              <span class="font-serif text-sm font-bold text-zinc-200 tracking-widest uppercase">{{
                authStore.user?.username || 'Guerreiro'
              }}</span>
              <span class="font-sans text-[9px] text-zinc-600 uppercase tracking-wider"
                >Apto ao Salão</span
              >
            </div>
          </div>
        </div>

        <div
          class="mt-auto pb-8 font-sans text-[9px] text-zinc-700 uppercase tracking-widest leading-relaxed"
        >
          <p>MIDGARD EST. 2026 // FORJADO POR PAULO</p>
        </div>
      </aside>
    </div>

    <!-- TERMINAL MOBILE (Navegação Inferior) -->
    <nav
      class="md:hidden fixed bottom-0 w-full bg-black border-t border-zinc-900 flex justify-between items-center px-6 py-4 z-50"
    >
      <RouterLink to="/" class="text-white relative">
        <span
          class="absolute -top-4 left-1/2 -translate-x-1/2 w-1 h-1 bg-red-700 shadow-[0_0_8px_rgba(185,28,28,0.6)]"
        ></span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            fill-rule="evenodd"
            d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"
          />
          <path
            fill-rule="evenodd"
            d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"
          />
        </svg>
      </RouterLink>

      <RouterLink to="/busca" class="text-zinc-600 hover:text-zinc-300 transition-colors">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"
          />
        </svg>
      </RouterLink>

      <button
        @click.prevent="isCreateModalOpen = true"
        class="w-10 h-10 bg-zinc-100 text-black flex items-center justify-center hover:bg-red-700 hover:text-white transition-colors"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"
          />
        </svg>
      </button>

      <RouterLink to="/perfil" class="w-6 h-6 border border-zinc-800">
        <img
          :src="
            authStore.user?.avatar
              ? storageBaseUrl + authStore.user.avatar
              : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&fit=crop'
          "
          class="w-full h-full object-cover grayscale"
        />
      </RouterLink>
    </nav>

    <!-- Modais -->
    <CreatePostModal
      v-if="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @submit="handlePostSubmit"
    />

    <StoryViewerModal
      v-if="selectedMundoGroup"
      :userGroup="selectedMundoGroup"
      @close="selectedMundoGroup = null"
    />
  </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
