<script setup>
import { ref } from 'vue'
import MidgardLogo from '../components/MidgardLogo.vue'

const posts = ref([
  {
    id: 1,
    author: {
      username: 'gulosinho',
      avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&h=150&fit=crop',
    },

    image: 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&h=1067&fit=crop',
    likes: 142,
    caption: 'Configurando os novos containers do projeto. 🚀',
    comments: 12,
  },
  {
    id: 2,
    author: {
      username: 'cyber_night',
      avatar: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=150&h=150&fit=crop',
    },
    image: 'https://images.unsplash.com/photo-1507146426996-ef05306b995a?w=600&h=1067&fit=crop',
    likes: 89,
    caption: 'Análise de logs da madrugada concluída.',
    comments: 5,
  },
])
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col md:flex-row">
    <nav class="hidden md:flex flex-col w-64 border-r border-zinc-800 p-6 sticky top-0 h-screen">
      <div class="mb-10">
        <MidgardLogo />
      </div>

      <div class="flex flex-col gap-6 text-zinc-300">
        <a
          href="#"
          class="flex items-center gap-4 text-lg font-bold text-white transition-transform hover:scale-105"
        >
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
          Página Inicial
        </a>
        <a
          href="#"
          class="flex items-center gap-4 text-lg font-medium hover:text-white transition-colors"
        >
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
          Pesquisa
        </a>
        <a
          href="#"
          class="flex items-center gap-4 text-lg font-medium hover:text-white transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="currentColor"
            viewBox="0 0 16 16"
          >
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
          </svg>
          Perfil
        </a>
      </div>
    </nav>

    <!-- Área Principal do Feed -->
    <main class="flex-1 flex justify-center pb-20 md:pb-0">
      <div class="w-full max-w-[470px] mt-8 flex flex-col gap-10">
        <!-- Renderização Dinâmica dos Posts -->
        <article v-for="post in posts" :key="post.id" class="border-b border-zinc-800 pb-6">
          <!-- Cabeçalho do Post (Autor) -->
          <div class="flex items-center gap-3 mb-3 px-2 md:px-0">
            <img
              :src="post.author.avatar"
              alt="Avatar"
              class="w-8 h-8 rounded-full object-cover border border-zinc-700"
            />
            <span class="font-semibold text-sm text-zinc-100">{{ post.author.username }}</span>
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
            <button class="hover:text-zinc-400 transition-colors">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"
                />
              </svg>
            </button>
            <button class="hover:text-zinc-400 transition-colors">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.35-1.548.31-2.316-1.52-1.21-2.45-2.82-2.45-4.504C0 3.738 3.582 1 8 1s8 2.738 8 5-3.582 5-8 5c-1.44 0-2.806-.277-4.009-.776a17 17 0 0 1-1.785.672Q2.735 15.423 2.185 15.8"
                />
              </svg>
            </button>
          </div>

          <!-- Likes e Legenda -->
          <div class="mt-2 px-2 md:px-0">
            <p class="text-sm font-semibold mb-1">{{ post.likes }} curtidas</p>
            <p class="text-sm">
              <span class="font-semibold mr-2">{{ post.author.username }}</span>
              <span class="text-zinc-300">{{ post.caption }}</span>
            </p>
            <p class="text-zinc-500 text-sm mt-1 cursor-pointer">
              Ver todos os {{ post.comments }} comentários
            </p>
          </div>
        </article>
      </div>
    </main>

    <!-- Navegação Inferior (Mobile) -->
    <nav
      class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-800 flex justify-around p-3 z-50"
    >
      <a href="#" class="text-white"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"
          />
          <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" /></svg
      ></a>
      <a href="#" class="text-zinc-500"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"
          /></svg
      ></a>
      <a href="#" class="text-zinc-500"
        ><svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" /></svg
      ></a>
    </nav>
  </div>
</template>
