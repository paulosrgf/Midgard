<script setup>
import { ref } from 'vue'
import MidgardLogo from '../components/MidgardLogo.vue'
import PostCard from '../components/PostCard.vue'

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
    <!-- Navegação Lateral (Desktop) -->
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
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
      </div>
    </main>

    <!-- Navegação Inferior (Mobile) -->
    <nav
      class="md:hidden fixed bottom-0 w-full bg-zinc-950 border-t border-zinc-800 flex justify-around p-3 z-50"
    >
      <a href="#" class="text-white">
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
      </a>
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
      <a href="#" class="text-zinc-500">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
        </svg>
      </a>
    </nav>
  </div>
</template>
