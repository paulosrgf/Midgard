<script setup>
import { ref } from 'vue'
import api from '@/services/api'

// O defineProps diz ao Vue que este componente espera receber um objeto chamado 'post'
const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
})

// Estados reativos locais para gerenciar curtidas e comentários
const likesCount = ref(props.post.likes)
const isLiked = ref(props.post.isLiked || false)
const commentsList = ref(props.post.comments || [])
const newCommentText = ref('')
const showCommentsModal = ref(false) // Opcional caso queira expandir os comentários

// Função para Curtir / Descurtir
const toggleLike = async () => {
  try {
    const response = await api.post(`/posts/${props.post.id}/like`)
    likesCount.value = response.data.likes_count
    isLiked.value = response.data.liked
  } catch (error) {
    console.error('Erro ao curtir postagem:', error)
  }
}

// Função para Adicionar Comentário
const addComment = async () => {
  if (!newCommentText.value.trim()) return

  try {
    const response = await api.post(`/posts/${props.post.id}/comments`, {
      body: newCommentText.value,
    })
    // Adiciona o comentário recém-criado no topo da lista
    commentsList.value.unshift(response.data)
    newCommentText.value = ''
  } catch (error) {
    console.error('Erro ao comentar:', error)
  }
}
</script>

<template>
  <article class="border-b border-zinc-800 pb-6">
    <!-- Cabeçalho do Post (Autor) -->
    <div class="flex items-center gap-3 mb-3 px-2 md:px-0">
      <img
        :src="post.author.avatar"
        alt="Avatar"
        class="w-8 h-8 rounded-full object-cover border border-zinc-700"
      />
      <RouterLink
        :to="'/u/' + post.author.username"
        class="font-semibold text-sm text-zinc-100 hover:underline"
        >{{ post.author.username }}</RouterLink
      >
      <span class="text-zinc-500 text-xs">• 2 h</span>
    </div>

    <!-- Mídia do Post -->
    <div class="w-full rounded bg-zinc-900 border border-zinc-800 overflow-hidden">
      <img
        :src="post.image"
        alt="Post content"
        class="w-full h-auto object-cover aspect-[4/5] md:aspect-[9/16]"
      />
    </div>

    <!-- Ações do Post (Like, Comment) -->
    <div class="flex items-center gap-4 mt-3 px-2 md:px-0">
      <!-- Botão de Curtir -->
      <button
        @click="toggleLike"
        class="transition-colors focus:outline-none"
        :class="isLiked ? 'text-red-500 hover:text-red-400' : 'hover:text-zinc-400'"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          :fill="isLiked ? 'currentColor' : 'none'"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
          />
        </svg>
      </button>

      <!-- Ícone de Balão de Comentário -->
      <button class="hover:text-zinc-400 transition-colors">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A7.596 7.596 0 0012 20.25z"
          />
        </svg>
      </button>
    </div>

    <!-- Likes e Legenda -->
    <div class="mt-2 px-2 md:px-0">
      <p class="text-sm font-semibold mb-1">{{ likesCount }} curtidas</p>
      <p class="text-sm">
        <span class="font-semibold mr-2">{{ post.author.username }}</span>
        <span class="text-zinc-300">{{ post.caption }}</span>
      </p>

      <!-- Seção de Comentários Existentes -->
      <div class="mt-2 space-y-1 max-h-32 overflow-y-auto">
        <p v-for="comment in commentsList" :key="comment.id" class="text-sm">
          <span class="font-semibold mr-2 text-zinc-200">{{ comment.username }}</span>
          <span class="text-zinc-400">{{ comment.body }}</span>
        </p>
      </div>

      <p v-if="commentsList.length === 0" class="text-zinc-500 text-sm mt-1">
        Nenhum comentário ainda. Seja o primeiro!
      </p>

      <!-- Input para Novo Comentário -->
      <form
        @submit.prevent="addComment"
        class="mt-3 flex items-center border-t border-zinc-800 pt-2"
      >
        <input
          v-model="newCommentText"
          type="text"
          placeholder="Adicione um comentário..."
          class="w-full bg-transparent text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none"
        />
        <button
          type="submit"
          :disabled="!newCommentText.trim()"
          class="text-sm font-semibold text-sky-500 hover:text-sky-400 disabled:opacity-50 ml-2"
        >
          Publicar
        </button>
      </form>
    </div>
  </article>
</template>
