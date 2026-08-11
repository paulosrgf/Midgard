<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  userGroup: {
    type: Array,
    required: true,
  },
})
const emit = defineEmits(['close'])

const currentIndex = ref(0)
const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const currentStory = computed(() => props.userGroup[currentIndex.value])

// FUNÇÕES DE SEGURANÇA PARA AS IMAGENS (Supabase + Fallback)
const getAvatarUrl = (user) => {
  if (!user.avatar) {
    return `https://ui-avatars.com/api/?name=${user.username}&background=18181b&color=ef4444&bold=true`
  }
  return user.avatar.startsWith('http') ? user.avatar : storageBaseUrl + user.avatar
}

const getStoryMediaUrl = (story) => {
  // Pega o caminho, independente se no seu banco chama image_path ou media_path
  const path = story.image_path || story.media_path
  if (!path) return ''
  return path.startsWith('http') ? path : storageBaseUrl + path
}

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
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm">
    <button
      @click="$emit('close')"
      class="absolute top-6 right-6 text-zinc-400 hover:text-white p-2"
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

      <!-- Info do Usuário -->
      <div class="absolute top-8 left-4 flex items-center gap-3 z-20">
        <!-- Avatar Limpo -->
        <img
          :src="getAvatarUrl(currentStory.user)"
          class="w-10 h-10 rounded-full border border-zinc-700 shadow-md object-cover"
        />
        <span class="text-white font-serif tracking-widest text-sm drop-shadow-md uppercase">{{
          currentStory.user.username
        }}</span>
      </div>

      <!-- A Imagem do Story Limpa -->
      <div class="flex-1 w-full h-full relative" @click="nextStory">
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
