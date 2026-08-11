<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const props = defineProps({
  userGroup: {
    type: Array,
    required: true,
  },
})
// 1ª CORREÇÃO: Adicionado o evento 'deleted'
const emit = defineEmits(['close', 'deleted'])

const authStore = useAuthStore()
const currentIndex = ref(0)
const isDeleting = ref(false)

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const currentStory = computed(() => props.userGroup[currentIndex.value])

// --- SISTEMA TEMPORAL ---
const currentTime = ref(new Date())
let timerInterval

onMounted(() => {
  timerInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 60000)
})

onUnmounted(() => {
  clearInterval(timerInterval)
})

const getTimeAgo = (dateString) => {
  if (!dateString) return ''
  const now = currentTime.value
  const past = new Date(dateString)
  const diffMs = now - past
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffMinutes = Math.floor(diffMs / (1000 * 60))

  if (diffHours >= 24) return 'Expirando...'
  if (diffHours >= 1) return `${diffHours} h`
  if (diffMinutes > 0) return `${diffMinutes} m`
  return 'Agora mesmo'
}

// --- FUNÇÕES DE SEGURANÇA (Supabase) ---
const getAvatarUrl = (user) => {
  if (!user.avatar) {
    return `https://ui-avatars.com/api/?name=${user.username}&background=18181b&color=ef4444&bold=true`
  }
  return user.avatar.startsWith('http') ? user.avatar : storageBaseUrl + user.avatar
}

const getStoryMediaUrl = (story) => {
  const path = story.image_path || story.media_path
  if (!path) return ''
  return path.startsWith('http') ? path : storageBaseUrl + path
}

// --- NAVEGAÇÃO E DESTRUIÇÃO ---
const nextStory = () => {
  if (currentIndex.value < props.userGroup.length - 1) {
    currentIndex.value++
  } else {
    emit('close')
  }
}

const prevStory = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

const deleteCurrentStory = async () => {
  if (!confirm('Tem certeza que deseja destruir este Mundo?')) return

  isDeleting.value = true
  try {
    await api.delete(`/stories/${currentStory.value.id}`)

    // Remove o story do array visualmente
    props.userGroup.splice(currentIndex.value, 1)

    // 2ª CORREÇÃO: Avisa a Home para recarregar o banco
    emit('deleted')

    // Se ele apagou o último story que restava, fecha o modal
    if (props.userGroup.length === 0) {
      emit('close')
    } else {
      // Se apagou o último da fila, mas ainda tem outros anteriores, volta o índice
      if (currentIndex.value >= props.userGroup.length) {
        currentIndex.value = props.userGroup.length - 1
      }
    }
  } catch (error) {
    console.error('Erro ao destruir Mundo:', error)
    alert('Os deuses impediram a destruição. Tente novamente.')
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm">
    <!-- Botão de Fechar -->
    <button
      @click="$emit('close')"
      class="absolute top-6 right-6 text-zinc-400 hover:text-white p-2 z-50"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="32"
        height="32"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <div
      class="relative w-full max-w-md h-[80vh] bg-zinc-950 rounded-xl overflow-hidden shadow-2xl shadow-red-900/10 border border-zinc-900 flex flex-col"
    >
      <!-- Barras de Progresso -->
      <div class="absolute top-0 inset-x-0 p-4 flex gap-1 z-20">
        <div
          v-for="(story, index) in userGroup"
          :key="story.id"
          class="h-1 flex-1 bg-zinc-600/50 rounded-full overflow-hidden"
        >
          <div
            class="h-full bg-red-600 transition-all duration-300"
            :class="index <= currentIndex ? 'w-full' : 'w-0'"
          ></div>
        </div>
      </div>

      <!-- Info do Usuário e Tempo -->
      <div class="absolute top-8 left-4 flex items-center gap-3 z-20">
        <img
          :src="getAvatarUrl(currentStory.user)"
          class="w-10 h-10 rounded-full border border-zinc-700 shadow-md object-cover"
        />
        <div class="flex flex-col drop-shadow-md">
          <span class="text-white font-serif tracking-widest text-sm uppercase leading-tight">{{
            currentStory.user.username
          }}</span>
          <span class="text-zinc-300 font-sans text-xs font-bold tracking-wider opacity-80">{{
            getTimeAgo(currentStory.created_at)
          }}</span>
        </div>
      </div>

      <!-- Botão de Excluir (Só aparece se você for o autor) -->
      <button
        v-if="authStore.user?.id === currentStory.user.id"
        @click.stop="deleteCurrentStory"
        :disabled="isDeleting"
        class="absolute top-8 right-4 text-zinc-400 hover:text-red-500 p-2 z-50 transition-colors drop-shadow-md bg-black/40 rounded-full backdrop-blur-sm"
        title="Destruir Mundo"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
          />
        </svg>
      </button>

      <!-- A Imagem do Story -->
      <div class="flex-1 w-full h-full relative cursor-pointer" @click="nextStory">
        <img :src="getStoryMediaUrl(currentStory)" class="w-full h-full object-cover" alt="Mundo" />
      </div>

      <!-- Área de Clique Esquerda para voltar -->
      <div
        class="absolute inset-y-0 left-0 w-1/3 z-10 cursor-pointer"
        @click.stop="prevStory"
      ></div>
    </div>
  </div>
</template>
