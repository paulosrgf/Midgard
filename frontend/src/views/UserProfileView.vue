<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import MidgardLogo from '../components/MidgardLogo.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const profile = ref(null)
const loading = ref(true)
const isFollowLoading = ref(false)

// Busca os dados do usuário baseado na URL (/u/:username)
const fetchUserProfile = async (username) => {
  loading.value = true
  try {
    const response = await api.get(`/users/${username}`)
    profile.value = response.data
  } catch (error) {
    console.error('Erro ao buscar usuário:', error)
    // Se o usuário não existir, joga de volta pro feed
    router.push('/')
  } finally {
    loading.value = false
  }
}

// Lógica de Seguir / Deixar de seguir
const toggleFollow = async () => {
  if (isFollowLoading.value) return
  isFollowLoading.value = true

  try {
    const response = await api.post(`/users/${profile.value.user.username}/follow`)
    // Atualiza o estado localmente sem precisar recarregar a página inteira
    profile.value.user.is_followed_by_me = response.data.following
    profile.value.user.followers_count = response.data.followers_count
  } catch (error) {
    console.error('Erro ao seguir/deixar de seguir:', error)
  } finally {
    isFollowLoading.value = false
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

// Executa na montagem e também escuta mudanças na URL (se pular de um perfil pra outro)
onMounted(() => fetchUserProfile(route.params.username))
watch(
  () => route.params.username,
  (newUsername) => {
    if (newUsername) fetchUserProfile(newUsername)
  },
)
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex justify-center relative">
    <div class="w-full max-w-[1200px] flex flex-col md:flex-row justify-between">
      <!-- 1. Navegação Lateral Esquerda (Menu Global) -->
      <nav
        class="hidden md:flex flex-col w-[240px] xl:w-[260px] border-r border-zinc-900 p-4 sticky top-0 h-screen shrink-0 justify-between"
      >
        <div>
          <div class="mb-8 px-2 pt-4">
            <MidgardLogo />
          </div>
          <div class="flex flex-col gap-1 text-zinc-200">
            <RouterLink
              to="/"
              class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                class="group-hover:scale-105 transition-transform"
                viewBox="0 0 16 16"
              >
                <path
                  d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"
                />
                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
              </svg>
              <span class="text-[15px] font-medium group-hover:text-white">Página Inicial</span>
            </RouterLink>
            <!-- Outros links de menu... -->
            <RouterLink
              to="/perfil"
              class="group flex items-center gap-4 p-3 rounded-lg hover:bg-zinc-900/60 transition-colors"
            >
              <img
                src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop"
                alt="Meu Perfil"
                class="w-6 h-6 rounded-full object-cover border border-zinc-700"
              />
              <span class="text-[15px] font-medium group-hover:text-white">Perfil</span>
            </RouterLink>
          </div>
        </div>
        <button
          @click="handleLogout"
          class="group flex items-center gap-4 p-3 rounded-lg hover:bg-red-950/40 text-zinc-200 transition-colors mb-4 w-full text-left"
        >
          <!-- Ícone de sair... -->
          <span class="text-[15px] font-medium group-hover:text-red-500 transition-colors"
            >Sair</span
          >
        </button>
      </nav>

      <!-- 2. Área Principal do Perfil Alheio -->
      <main
        class="flex-1 w-full max-w-[935px] mx-auto pb-20 md:pb-8 pt-8 px-4 md:px-16"
        v-if="!loading && profile"
      >
        <!-- Header -->
        <header class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-20 mb-12">
          <div class="shrink-0 relative">
            <img
              :src="profile.user.avatar"
              alt="Avatar"
              class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border border-zinc-800 p-1"
            />
          </div>

          <div class="flex-1 flex flex-col items-center md:items-start">
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
              <h1 class="text-xl md:text-2xl font-medium text-zinc-100">
                {{ profile.user.username }}
              </h1>

              <!-- Botão de Follow Dinâmico (Esconde se for o próprio usuário logado acessando sua própria URL) -->
              <div v-if="authStore.user?.username !== profile.user.username" class="flex gap-2">
                <button
                  @click="toggleFollow"
                  :disabled="isFollowLoading"
                  :class="[
                    'px-6 py-1.5 rounded-lg text-sm font-bold transition-colors disabled:opacity-50',
                    profile.user.is_followed_by_me
                      ? 'bg-zinc-800 hover:bg-zinc-700 text-white'
                      : 'bg-indigo-600 hover:bg-indigo-500 text-white',
                  ]"
                >
                  {{ profile.user.is_followed_by_me ? 'Seguindo' : 'Seguir' }}
                </button>
              </div>
            </div>

            <div class="hidden md:flex gap-10 text-[15px] mb-5 text-zinc-100">
              <p>
                <span class="font-bold">{{ profile.user.posts_count }}</span> publicações
              </p>
              <p>
                <span class="font-bold">{{ profile.user.followers_count }}</span> seguidores
              </p>
              <p>
                <span class="font-bold">{{ profile.user.following_count }}</span> seguindo
              </p>
            </div>

            <div class="text-[15px] text-center md:text-left">
              <p class="font-semibold text-zinc-100 mb-0.5">{{ profile.user.name }}</p>
              <p class="text-zinc-400 whitespace-pre-line">{{ profile.user.bio }}</p>
            </div>
          </div>
        </header>

        <!-- Status Mobile -->
        <div
          class="flex md:hidden justify-around border-t border-zinc-800 py-3 text-sm text-center text-zinc-100 mb-4"
        >
          <div class="flex flex-col">
            <span class="font-bold">{{ profile.user.posts_count }}</span>
            <span class="text-zinc-500 text-xs">publicações</span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold">{{ profile.user.followers_count }}</span>
            <span class="text-zinc-500 text-xs">seguidores</span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold">{{ profile.user.following_count }}</span>
            <span class="text-zinc-500 text-xs">seguindo</span>
          </div>
        </div>

        <div
          class="border-t border-zinc-800 flex justify-center uppercase text-xs font-bold tracking-widest mt-4 md:mt-0"
        >
          <div class="flex gap-12">
            <button class="flex items-center gap-2 py-4 border-t border-white text-white">
              Publicações
            </button>
          </div>
        </div>

        <!-- Grid de Fotos -->
        <div class="grid grid-cols-3 gap-1 md:gap-4 mt-2">
          <div
            v-for="post in profile.posts"
            :key="post.id"
            class="aspect-square relative group cursor-pointer bg-zinc-900 rounded overflow-hidden"
          >
            <img
              :src="post.image"
              alt="Post"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div
              class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-6 transition-opacity duration-300"
            >
              <span class="flex items-center gap-2 font-bold text-white text-lg"
                >❤️ {{ post.likes_count }}</span
              >
              <span class="flex items-center gap-2 font-bold text-white text-lg"
                >💬 {{ post.comments_count }}</span
              >
            </div>
          </div>
        </div>
      </main>

      <div v-else class="flex-1 flex justify-center items-center h-screen text-zinc-500">
        <svg
          class="animate-spin h-8 w-8 text-zinc-500"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
      </div>
    </div>
  </div>
</template>
