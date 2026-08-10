<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import PostCard from '../components/PostCard.vue'

const authStore = useAuthStore()
const posts = ref([])
const isEditing = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const fetchMyPosts = async () => {
  try {
    const response = await api.get(`/users/${authStore.user.id}/posts`)
    posts.value = response.data
  } catch (error) {
    console.error('Erro ao buscar sagas:', error)
  }
}

onMounted(() => {
  fetchMyPosts()
})
</script>

<template>
  <main
    class="flex-1 flex justify-center w-full max-w-[720px] mx-auto pb-24 md:pb-0 bg-black border-r border-zinc-900"
  >
    <div class="w-full flex flex-col">
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
                authStore.user?.avatar
                  ? storageBaseUrl + authStore.user.avatar
                  : 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=300&fit=crop'
              "
              class="w-full h-full object-cover grayscale"
            />
          </div>
          <button
            @click="isEditing = !isEditing"
            class="border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-400 font-sans text-[10px] uppercase tracking-widest px-6 py-3 transition-colors bg-black"
          >
            Editar Perfil
          </button>
        </div>

        <h1 class="font-serif text-3xl uppercase tracking-widest text-white mb-2">
          {{ authStore.user?.name || authStore.user?.username }}
        </h1>
        <p class="font-sans text-sm text-zinc-500">@{{ authStore.user?.username }}</p>

        <div class="flex gap-8 mt-8 pt-6 border-t border-zinc-900">
          <div class="flex flex-col text-center">
            <span class="font-serif text-2xl text-zinc-100">{{ posts.length }}</span>
            <span class="font-sans text-[10px] uppercase tracking-widest text-zinc-600 mt-1"
              >Sagas</span
            >
          </div>
          <div class="flex flex-col text-center">
            <span class="font-serif text-2xl text-zinc-100">0</span>
            <span class="font-sans text-[10px] uppercase tracking-widest text-zinc-600 mt-1"
              >Guerreiros</span
            >
          </div>
        </div>
      </div>

      <div class="w-full bg-black">
        <div class="border-y border-zinc-900 p-4 text-center">
          <span class="font-serif text-lg tracking-widest uppercase text-zinc-300"
            >Minhas Sagas</span
          >
        </div>

        <div
          v-if="posts.length === 0"
          class="p-16 text-center text-zinc-600 font-sans text-sm tracking-widest uppercase"
        >
          Sua espada ainda está limpa. Nenhuma saga registrada.
        </div>

        <div v-else class="flex flex-col gap-px bg-zinc-900">
          <PostCard
            v-for="post in posts"
            :key="post.id"
            :post="post"
            @deleted="fetchMyPosts"
            class="bg-black rounded-none"
          />
        </div>
      </div>
    </div>
  </main>
</template>
