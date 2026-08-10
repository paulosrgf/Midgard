<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'
import PostCard from '../components/PostCard.vue'

const route = useRoute()
const user = ref(null)
const posts = ref([])
const notFound = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const fetchUserData = async () => {
  try {
    const resUser = await api.get(`/users/${route.params.id}`)
    user.value = resUser.data
    const resPosts = await api.get(`/users/${route.params.id}/posts`)
    posts.value = resPosts.data
  } catch (error) {
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
      <div class="relative w-full h-48 bg-zinc-950 border-b border-zinc-900 overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1542223189-92d8cb29094e?q=80&w=2000&auto=format&fit=crop"
          class="w-full h-full object-cover grayscale opacity-30 mix-blend-luminosity"
        />
      </div>

      <div class="px-4 md:px-8 pb-10 relative">
        <div class="flex justify-between items-end -mt-16 mb-6">
          <div class="w-32 h-32 bg-black border-2 border-zinc-800 relative z-10 overflow-hidden">
            <img
              :src="
                user.avatar
                  ? storageBaseUrl + user.avatar
                  : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=300&fit=crop'
              "
              class="w-full h-full object-cover grayscale"
            />
          </div>
          <button
            class="bg-zinc-100 text-black hover:bg-red-700 hover:text-white font-sans text-[10px] font-bold uppercase tracking-widest px-8 py-3 transition-colors"
          >
            Acompanhar
          </button>
        </div>

        <h1 class="font-serif text-3xl uppercase tracking-widest text-white mb-2">
          {{ user.name || user.username }}
        </h1>
        <p class="font-sans text-sm text-zinc-500">@{{ user.username }}</p>
      </div>

      <div class="w-full bg-black">
        <div class="border-y border-zinc-900 p-4 text-center">
          <span class="font-serif text-lg tracking-widest uppercase text-zinc-300">Sagas</span>
        </div>

        <div
          v-if="posts.length === 0"
          class="p-16 text-center text-zinc-600 font-sans text-sm tracking-widest uppercase"
        >
          A lenda deste guerreiro ainda não começou.
        </div>

        <div v-else class="flex flex-col gap-px bg-zinc-900">
          <PostCard
            v-for="post in posts"
            :key="post.id"
            :post="post"
            class="bg-black rounded-none"
          />
        </div>
      </div>
    </div>
  </main>
</template>
