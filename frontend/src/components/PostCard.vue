<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
})

// Permite avisar a tela (Home ou Perfil) que o post foi deletado
const emit = defineEmits(['deleted'])

const authStore = useAuthStore()

const likesCount = ref(props.post.likes)
const isLiked = ref(props.post.isLiked || false)
const commentsList = ref(props.post.comments || [])
const newCommentText = ref('')
const isDeleting = ref(false)

const isLiking = ref(false) // Nova variável para travar duplo clique

const toggleLike = async () => {
  if (isLiking.value) return // Aborta se já estiver carregando

  isLiking.value = true
  try {
    const response = await api.post(`/posts/${props.post.id}/like`)
    likesCount.value = response.data.likes_count
    isLiked.value = response.data.liked
  } catch (error) {
    console.error('Erro ao curtir postagem:', error)
  } finally {
    isLiking.value = false
  }
}

const addComment = async () => {
  if (!newCommentText.value.trim()) return
  try {
    const response = await api.post(`/posts/${props.post.id}/comments`, {
      body: newCommentText.value,
    })
    commentsList.value.unshift(response.data)
    newCommentText.value = ''
  } catch (error) {
    console.error('Erro ao comentar:', error)
  }
}

// NOVA FUNÇÃO DE EXCLUSÃO
const deletePost = async () => {
  if (!confirm('Tem certeza que deseja excluir esta publicação? Essa ação não pode ser desfeita.'))
    return

  isDeleting.value = true
  try {
    await api.delete(`/posts/${props.post.id}`)
    // Avisa a tela para remover o post da lista visualmente
    emit('deleted', props.post.id)
  } catch (error) {
    console.error('Erro ao excluir post:', error)
    alert('Erro ao excluir a publicação.')
    isDeleting.value = false
  }
}
</script>

<template>
  <article v-if="!isDeleting" class="border-b border-zinc-800 pb-6">
    <!-- Cabeçalho do Post com Ícone de Lixeira -->
    <div class="flex items-center justify-between mb-3 px-2 md:px-0">
      <div class="flex items-center gap-3">
        <img
          :src="post.author.avatar"
          alt="Avatar"
          class="w-8 h-8 rounded-full object-cover border border-zinc-700"
        />
        <!-- Link para o Perfil -->
        <RouterLink
          :to="'/u/' + post.author.username"
          class="font-semibold text-sm text-zinc-100 hover:underline"
        >
          {{ post.author.username }}
        </RouterLink>
        <span class="text-zinc-500 text-xs">• 2 h</span>
      </div>

      <!-- LIXEIRA: Só aparece se o usuário logado for o dono da postagem -->
      <button
        v-if="authStore.user?.username === post.author.username"
        @click="deletePost"
        class="text-zinc-500 hover:text-red-500 transition-colors p-1"
        title="Excluir publicação"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
          />
        </svg>
      </button>
    </div>

    <!-- Mídia do Post -->
    <div class="w-full rounded bg-zinc-900 border border-zinc-800 overflow-hidden">
      <img
        :src="post.image"
        alt="Post content"
        class="w-full h-auto object-cover aspect-[4/5] md:aspect-[9/16]"
      />
    </div>

    <!-- Ações (Like, Comment) -->
    <div class="flex items-center gap-4 mt-3 px-2 md:px-0">
      <!-- Botão de Curtir -->
      <button
        @click.prevent="toggleLike"
        :disabled="isLiking"
        class="transition-colors focus:outline-none disabled:opacity-50"
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

      <!-- Ícone de Comentário -->
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
        <RouterLink :to="'/u/' + post.author.username" class="font-semibold mr-2 hover:underline">{{
          post.author.username
        }}</RouterLink>
        <span class="text-zinc-300">{{ post.caption }}</span>
      </p>

      <!-- Lista de Comentários -->
      <div class="mt-2 space-y-1 max-h-32 overflow-y-auto">
        <p v-for="comment in commentsList" :key="comment.id" class="text-sm">
          <RouterLink
            :to="'/u/' + comment.username"
            class="font-semibold mr-2 text-zinc-200 hover:underline"
            >{{ comment.username }}</RouterLink
          >
          <span class="text-zinc-400">{{ comment.body }}</span>
        </p>
      </div>

      <!-- Input Comentário -->
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
