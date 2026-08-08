<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import MidgardLogo from '../components/MidgardLogo.vue'

const router = useRouter()
const authStore = useAuthStore()
const profile = ref(null)
const loading = ref(true)

// Buscar dados do perfil e posts
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

onMounted(() => {
  fetchProfile()
})
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex justify-center relative">
    <div class="w-full max-w-[1200px] flex flex-col md:flex-row justify-between">
      <!-- 1. Navegação Lateral Esquerda (Exatamente igual à Home) -->
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

            <a
              href="#"
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
                  d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"
                />
              </svg>
              <span class="text-[15px] font-medium group-hover:text-white">Pesquisa</span>
            </a>

            <!-- Botão Perfil (Ativo) -->
            <RouterLink
              to="/perfil"
              class="group flex items-center gap-4 p-3 rounded-lg bg-zinc-900 text-white transition-colors"
            >
              <img
                src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop"
                alt="Perfil"
                class="w-6 h-6 rounded-full object-cover border border-zinc-700"
              />
              <span class="text-[15px] font-bold">Perfil</span>
            </RouterLink>
          </div>
        </div>

        <button
          @click="handleLogout"
          class="group flex items-center gap-4 p-3 rounded-lg hover:bg-red-950/40 text-zinc-200 transition-colors mb-4 w-full text-left"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="currentColor"
            class="group-hover:text-red-500 transition-colors"
            viewBox="0 0 16 16"
          >
            <path
              fill-rule="evenodd"
              d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"
            />
            <path
              fill-rule="evenodd"
              d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"
            />
          </svg>
          <span class="text-[15px] font-medium group-hover:text-red-500 transition-colors"
            >Sair</span
          >
        </button>
      </nav>

      <!-- 2. Área Principal do Perfil -->
      <main
        class="flex-1 w-full max-w-[935px] mx-auto pb-20 md:pb-8 pt-8 px-4 md:px-16"
        v-if="!loading && profile"
      >
        <!-- Header do Perfil -->
        <header class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-20 mb-12">
          <!-- Avatar Grande -->
          <div class="shrink-0 relative">
            <img
              :src="profile.user.avatar"
              alt="Avatar"
              class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border border-zinc-800 p-1"
            />
          </div>

          <div class="flex-1 flex flex-col items-center md:items-start">
            <!-- Row 1: Username & Ações -->
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
              <h1 class="text-xl md:text-2xl font-medium text-zinc-100">
                {{ profile.user.username }}
              </h1>
              <div class="flex gap-2">
                <button
                  class="bg-zinc-800 hover:bg-zinc-700 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors text-white"
                >
                  Editar Perfil
                </button>
                <button
                  class="bg-zinc-800 hover:bg-zinc-700 px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors text-white"
                >
                  Ver arquivo
                </button>
                <button class="p-1.5 hover:text-zinc-400 transition-colors">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="currentColor"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Row 2: Status (Oculto no mobile, mostramos embaixo depois se quiser) -->
            <div class="hidden md:flex gap-10 text-[15px] mb-5 text-zinc-100">
              <p>
                <span class="font-bold">{{ profile.user.posts_count }}</span> publicações
              </p>
              <p><span class="font-bold">482</span> seguidores</p>
              <p><span class="font-bold">294</span> seguindo</p>
            </div>

            <!-- Row 3: Bio -->
            <div class="text-[15px] text-center md:text-left">
              <p class="font-semibold text-zinc-100 mb-0.5">{{ profile.user.username }}</p>
              <p class="text-zinc-400 whitespace-pre-line">FullStack Dev | DevOps & Cloud</p>
            </div>
          </div>
        </header>

        <!-- Status Mobile (Só aparece no celular) -->
        <div
          class="flex md:hidden justify-around border-t border-zinc-800 py-3 text-sm text-center text-zinc-100 mb-4"
        >
          <div class="flex flex-col">
            <span class="font-bold">{{ profile.user.posts_count }}</span>
            <span class="text-zinc-500 text-xs">publicações</span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold">482</span> <span class="text-zinc-500 text-xs">seguidores</span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold">294</span> <span class="text-zinc-500 text-xs">seguindo</span>
          </div>
        </div>

        <!-- Abas Navegacionais -->
        <div
          class="border-t border-zinc-800 flex justify-center uppercase text-xs font-bold tracking-widest mt-4 md:mt-0"
        >
          <div class="flex gap-12">
            <button class="flex items-center gap-2 py-4 border-t border-white text-white">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M10.5 1h-5A1.5 1.5 0 0 0 4 2.5v11A1.5 1.5 0 0 0 5.5 15h5a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 10.5 1M5.5 2h5a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5"
                />
              </svg>
              Publicações
            </button>
            <button
              class="flex items-center gap-2 py-4 border-t border-transparent text-zinc-500 hover:text-zinc-300 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z"
                />
              </svg>
              Salvos
            </button>
            <button
              class="flex items-center gap-2 py-4 border-t border-transparent text-zinc-500 hover:text-zinc-300 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.66 9.73c-.25.56-.66.86-1.12.96-.34.08-.7.01-1.02-.15l-.2-.1a2.6 2.6 0 0 1-1.05-1.16c-.22-.44-.31-1-.18-1.5l.08-.3c.1-.38.25-.75.46-1.1a1.9 1.9 0 0 1 .84-.7c.36-.16.76-.23 1.15-.17.43.06.84.27 1.15.59.34.35.53.8.53 1.28 0 .44-.13.84-.44 1.15zM6.58 6.07c-.4.36-.8.8-1.12 1.3-.3.47-.48 1-.5 1.54-.03.55.15 1.1.48 1.54.34.45.8.84 1.3 1.13.5.28 1.05.42 1.62.4.56-.02 1.1-.2 1.55-.54.43-.33.78-.77 1.02-1.28l.24-.52c.18-.38.28-.8.3-1.22.02-.4-.06-.82-.23-1.18a3.1 3.1 0 0 0-.67-1.03 4.2 4.2 0 0 0-1.1-.78 4.7 4.7 0 0 0-1.4-.42c-.52-.07-1.05-.03-1.54.12-.46.15-.88.4-1.22.75z"
                />
              </svg>
              Marcados
            </button>
          </div>
        </div>

        <!-- Grid de Fotos Lindão -->
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
              <span class="flex items-center gap-2 font-bold text-white text-lg">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="22"
                  height="22"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                  />
                </svg>
                {{ post.likes_count }}
              </span>
              <span class="flex items-center gap-2 font-bold text-white text-lg">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="22"
                  height="22"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"
                  />
                </svg>
                {{ post.comments_count }}
              </span>
            </div>
          </div>
        </div>

        <div
          v-if="profile.posts.length === 0"
          class="flex flex-col items-center justify-center text-zinc-500 py-20"
        >
          <div
            class="w-16 h-16 rounded-full border-2 border-zinc-700 flex items-center justify-center mb-4"
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
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"
              />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-zinc-200 mb-2">Compartilhe fotos</h2>
          <p class="text-sm">Quando você compartilhar fotos, elas aparecerão no seu perfil.</p>
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

    <!-- Navegação Inferior (Mobile) - Exatamente igual à Home -->
    <nav
      class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-900 flex justify-around p-3 z-50"
    >
      <RouterLink to="/" class="text-zinc-500">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"
          />
          <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
        </svg>
      </RouterLink>

      <a href="#" class="text-zinc-500">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"
          />
        </svg>
      </a>

      <!-- Ícone Perfil Ativo (Branco) no Mobile -->
      <RouterLink to="/perfil" class="text-white">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
        </svg>
      </RouterLink>
    </nav>
  </div>
</template>
