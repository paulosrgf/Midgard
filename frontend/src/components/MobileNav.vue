<template>
  <nav
    class="md:hidden fixed bottom-0 left-0 w-full bg-black border-t border-zinc-900 z-50 flex justify-around p-4"
  >
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
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
