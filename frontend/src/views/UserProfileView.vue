<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const user = ref(null)
const posts = ref([])
const notFound = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const fetchUserData = async () => {
  // Pega o parâmetro independentemente de como foi nomeado no router (id, userId ou username)
  const identifier = route.params.id || route.params.userId || route.params.username

  if (!identifier) {
    notFound.value = true
    return
  }

  try {
    const resUser = await api.get(`/users/${identifier}`)
    user.value = resUser.data
    const resPosts = await api.get(`/users/${identifier}/posts`)
    posts.value = resPosts.data
  } catch (error) {
    console.error('Erro ao buscar perfil:', error)
    notFound.value = true
  }
}

onMounted(() => {
  fetchUserData()
})
</script>

<template>
  <main
    class="flex-1 flex justify-center w-full max-w-[720px] mx-auto pb-24 md:pb-0 bg-black border-r border-zinc-900 min-h-screen"
  >
    <div v-if="notFound" class="flex flex-col items-center justify-center w-full p-20 text-center">
      <h2 class="font-serif text-2xl uppercase tracking-widest text-zinc-500 mb-4">
        Guerreiro Caído
      </h2>
      <p class="font-sans text-sm text-zinc-600 uppercase tracking-widest">
        Este perfil se perdeu em Valhalla.
      </p>
    </div>

    <div v-else-if="user" class="w-full flex flex-col">
      <!-- Header do Perfil -->
      <div class="relative w-full h-56 bg-zinc-900 border-b border-zinc-800 overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2000&auto=format&fit=crop"
          class="w-full h-full object-cover grayscale opacity-40 mix-blend-overlay"
        />
      </div>

      <div class="px-6 md:px-10 pb-10 relative">
        <div class="flex justify-between items-end -mt-16 mb-6">
          <div
            class="w-32 h-32 bg-zinc-950 border-4 border-zinc-900 relative z-10 overflow-hidden rounded-xl shadow-lg"
          >
            <img
              :src="
                user.avatar
                  ? user.avatar.startsWith('http')
                    ? user.avatar
                    : storageBaseUrl + user.avatar
                  : 'https://ui-avatars.com/api/?name=' +
                    user.username +
                    '&background=18181b&color=ef4444&size=200&bold=true'
              "
              class="w-full h-full object-cover"
            />
          </div>
        </div>

        <h1 class="font-serif text-3xl uppercase tracking-widest text-white mb-2">
          {{ user.name || user.username }}
        </h1>
        <p class="font-sans text-sm text-zinc-500">@{{ user.username }}</p>

        <div class="flex gap-8 mt-8 pt-6 border-t border-zinc-900">
          <div class="flex flex-col text-center">
            <span class="font-serif text-2xl text-zinc-100">{{ posts.length }}</span>
            <span class="font-sans text-[10px] uppercase tracking-widest text-zinc-600 mt-1"
              >Sagas</span
            >
          </div>
        </div>
      </div>

      <!-- GRADE DE SAGAS ESTILO INSTAGRAM (3x3) -->
      <div class="w-full bg-black">
        <div class="border-y border-zinc-900 p-4 flex justify-center gap-12">
          <span
            class="font-serif text-xs tracking-widest uppercase text-white border-b border-white pb-1 cursor-pointer"
            >Grade</span
          >
        </div>

        <div
          v-if="posts.length === 0"
          class="p-16 text-center text-zinc-600 font-sans text-sm tracking-widest uppercase"
        >
          A lenda deste guerreiro ainda não começou.
        </div>

        <div v-else class="grid grid-cols-3 gap-1 bg-black">
          <div
            v-for="post in posts"
            :key="post.id"
            class="aspect-square bg-zinc-900 relative group cursor-pointer overflow-hidden border border-zinc-900"
          >
            <img
              :src="
                post.image_path
                  ? post.image_path.startsWith('http')
                    ? post.image_path
                    : storageBaseUrl + post.image_path
                  : ''
              "
              class="w-full h-full object-cover group-hover:opacity-70 transition-opacity duration-300"
              alt="Saga"
            />
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
