<script setup>
import { ref, computed } from 'vue'
import { RouterLink, RouterView, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import api from './services/api'
import CreatePostModal from './components/CreatePostModal.vue'
import MobileNav from './components/MobileNav.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isCreateModalOpen = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

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
    window.dispatchEvent(new CustomEvent('post-created'))
    isCreateModalOpen.value = false
  } catch (error) {
    console.error('Erro ao registrar a saga:', error)
    alert('Ocorreu um erro ao forjar a saga.')
  }
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-black text-zinc-100 flex flex-col items-center relative font-sans selection:bg-red-900/40 pb-20 md:pb-0"
  >
    <div
      :class="[
        'w-full max-w-[1280px] flex justify-center',
        !isAuthPage ? 'md:flex-row flex-col items-center md:items-start' : 'items-center',
      ]"
    >
      <!-- 1. BARRA ESQUERDA (DESKTOP) -->
      <nav
        v-if="!isAuthPage"
        class="hidden md:flex flex-col w-[260px] border-r border-zinc-900 p-6 sticky top-0 h-screen shrink-0 justify-between bg-black z-10"
      >
        <div>
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

      <!-- 2. ROUTER VIEW (CENTRALIZADO NO MOBILE) -->
      <div class="flex-1 flex w-full justify-center">
        <RouterView />
      </div>
    </div>

    <!-- Navegação Mobile Fixa no Rodapé -->
    <MobileNav v-if="!isAuthPage" @logout="handleLogout" />

    <!-- Modal Global -->
    <CreatePostModal
      v-if="isCreateModalOpen"
      @close="isCreateModalOpen = false"
      @submit="handlePostSubmit"
    />
  </div>
</template>
