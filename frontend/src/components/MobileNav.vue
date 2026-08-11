<template>
  <nav
    class="md:hidden fixed bottom-0 left-0 w-full bg-black/95 backdrop-blur-md border-t border-zinc-900 z-50 flex justify-around items-center p-3"
  >
    <!-- Home -->
    <RouterLink to="/" class="text-zinc-400 hover:text-white p-2" active-class="text-red-600">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path
          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10"
        />
      </svg>
    </RouterLink>

    <!-- Busca -->
    <RouterLink to="/busca" class="text-zinc-400 hover:text-white p-2" active-class="text-red-600">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </RouterLink>

    <!-- Perfil -->
    <RouterLink to="/perfil" class="text-zinc-400 hover:text-white p-2" active-class="text-red-600">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
    </RouterLink>

    <!-- Botão de Sair (Partir) no Mobile -->
    <button
      @click="$emit('logout')"
      class="text-zinc-600 hover:text-red-500 p-2 transition-colors"
      title="Partir do Salão"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M15 3H7a2 2 0 00-2 2v14a2 2 0 002 2h8m4-10l-4-4m0 0l4 4m-4-4v12"
        />
      </svg>
    </button>
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'
defineEmits(['logout'])
</script>
