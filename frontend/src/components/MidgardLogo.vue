<script setup>
import { RouterLink } from 'vue-router'
import vikingIcon from './icons/viking.png'
</script>

<template>
  <RouterLink to="/" class="flex items-center gap-3 transition-opacity hover:opacity-80 w-fit">
    <img :src="vikingIcon" alt="Logo Viking Midgard" class="w-10 h-10 object-contain invert" />
    <!-- Dica: A classe 'invert' do Tailwind inverte as cores. Se o seu ícone for preto, ele ficará branco para aparecer no tema dark. Se ele já for branco, basta apagar a classe 'invert'. -->

    <!-- Texto da Logo -->
    <h1 class="text-3xl font-serif italic tracking-wider text-zinc-100 m-0">Midgard</h1>
  </RouterLink>
</template>
