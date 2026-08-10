<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '../services/api'
import PostCard from '../components/PostCard.vue'
import StoryViewerModal from '../components/StoryViewerModal.vue'

const posts = ref([])
const mundos = ref({}) // Stories agora são Mundos
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

const openMundo = (userGroup) => {
  selectedMundoGroup.value = userGroup
}

onMounted(() => {
  fetchPosts()
  fetchMundos()
  window.addEventListener('post-created', fetchPosts)
})

onUnmounted(() => {
  window.removeEventListener('post-created', fetchPosts)
})
</script>

<template>
  <main
    class="flex-1 flex justify-center w-full max-w-[720px] mx-auto pb-20 md:pb-0 border-x border-zinc-900 bg-black selection:bg-red-900/40"
  >
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
        <!-- Seu componente PostCard deve seguir as bordas quadradas -->
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

  <!-- Modal visualizador adaptado -->
  <StoryViewerModal
    v-if="selectedMundoGroup"
    :userGroup="selectedMundoGroup"
    @close="selectedMundoGroup = null"
  />
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
