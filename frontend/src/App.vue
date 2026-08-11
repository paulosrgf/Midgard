<script setup>
import { ref, computed } from 'vue'
import { RouterLink, RouterView, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import api from './services/api'
import CreatePostModal from './components/CreatePostModal.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isCreateModalOpen = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

// Desativa os menus na tela de Login e Register
const isAuthPage = computed(() => {
  return ['login', 'register'].includes(route.name)
})

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

const handlePostSubmit = async (postData) => {
  try {
    const formData = new FormData()
    formData.append('image', postData.image)
    if (postData.caption) {
      formData.append('caption', postData.caption)
    }

    await api.post('/posts', formData)
    window.dispatchEvent(new CustomEvent('post-created')) // Avisa a Home para recarregar
    isCreateModalOpen.value = false
  } catch (error) {
    console.error('Erro ao registrar a saga:', error)
    alert('Ocorreu um erro ao forjar a saga.')
  }
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-black text-zinc-100 flex justify-center relative font-sans selection:bg-red-900/40"
  >
    <div
      :class="[
        'w-full max-w-[1280px] flex flex-col justify-between',
        !isAuthPage ? 'md:flex-row' : 'items-center',
      ]"
    >
      <!-- 1. BARRA ESQUERDA (DESKTOP) -->
      <nav
        v-if="!isAuthPage"
        class="hidden md:flex flex-col w-[260px] border-r border-zinc-900 p-6 sticky top-0 h-screen shrink-0 justify-between bg-black z-10"
      >
        <div>
          <!-- Logo Viking -->
          <div class="mb-12 pt-2">
            <h1 class="font-serif text-4xl tracking-[0.2em] uppercase text-zinc-100">Midgard</h1>
          </div>

          <div class="flex flex-col gap-2">
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

            <RouterLink
              to="/perfil"
              class="group flex items-center gap-4 p-3 border border-transparent hover:border-zinc-900 hover:bg-zinc-950 transition-all mt-4"
            >
              <div class="w-6 h-6 border border-zinc-800 overflow-hidden">
                <img
                  :src="
                    authStore.user?.avatar
                      ? authStore.user.avatar.startsWith('http')
                        ? authStore.user.avatar
                        : storageBaseUrl + authStore.user.avatar
                      : 'https://ui-avatars.com/api/?name=' +
                        (authStore.user?.username || 'Guerreiro') +
                        '&background=18181b&color=ef4444&bold=true'
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

      <!-- 2. ROUTER VIEW (INJETA AS PÁGINAS AQUI) -->
      <div
        :class="[
          'flex-1 flex w-full justify-between',
          isAuthPage ? 'justify-center items-center' : '',
        ]"
      >
        <RouterView />
      </div>
    </div>

    <!-- TERMINAL MOBILE GLOBAL (Navegação Inferior) -->
    <nav
      v-if="!isAuthPage"
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
              ? authStore.user.avatar.startsWith('http')
                ? authStore.user.avatar
                : storageBaseUrl + authStore.user.avatar
              : 'https://ui-avatars.com/api/?name=' +
                (authStore.user?.username || 'Guerreiro') +
                '&background=18181b&color=ef4444&bold=true'
          "
          class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
        />
      </RouterLink>
    </nav>

    <!-- Modal Global -->
    <CreatePostModal
      v-if="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @submit="handlePostSubmit"
    />
  </div>
</template>
