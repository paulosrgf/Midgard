<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  userGroup: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['close'])

const currentIndex = ref(0)
let timer = null

// O primeiro item do array sempre tem os dados do usuário (trazidos pelo 'with' do Laravel)
const author = props.userGroup[0].user

const nextStory = () => {
  if (currentIndex.value < props.userGroup.length - 1) {
    currentIndex.value++
    resetTimer()
  } else {
    emit('close') // Fecha se acabaram os stories dessa pessoa
  }
}

const prevStory = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    resetTimer()
  }
}

const resetTimer = () => {
  if (timer) clearTimeout(timer)
  // Auto avança em 5 segundos, exatamente como o plano pede
  timer = setTimeout(nextStory, 5000)
}

onMounted(() => {
  resetTimer()
})

onUnmounted(() => {
  if (timer) clearTimeout(timer)
})
</script>

<template>
  <div class="fixed inset-0 z-[100] bg-zinc-950 flex flex-col">
    
    <!-- Barras de progresso no topo -->
    <div class="absolute top-2 left-0 w-full flex gap-1 px-2 z-20">
      <div v-for="(story, index) in userGroup" :key="story.id" class="h-1 flex-1 bg-zinc-700 rounded-full overflow-hidden">
        <div 
          class="h-full bg-white transition-all duration-[5000ms] ease-linear" 
          :class="{
            'w-full': index < currentIndex, 
            'w-0': index > currentIndex,
            'w-full animate-progress': index === currentIndex
          }"
          :style="index === currentIndex ? 'transition: width 5s linear; width: 100%' : (index < currentIndex ? 'width: 100%' : 'width: 0%')"
        ></div>
      </div>
    </div>

    <!-- Header do Usuário -->
    <div class="absolute top-6 left-0 w-full p-4 flex items-center justify-between z-20 bg-gradient-to-b from-black/60 to-transparent">
      <div class="flex items-center gap-3">
        <img :src="author.avatar || 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=150&h=150&fit=crop'" class="w-10 h-10 rounded-full border border-zinc-500 object-cover" />
        <span class="font-bold text-white text-sm shadow-black drop-shadow-md">{{ author.username }}</span>
      </div>
      <button @click="emit('close')" class="text-white text-3xl font-bold drop-shadow-md">&times;</button>
    </div>

    <!-- Visualizador de Mídia com Zonas de Toque -->
    <div class="flex-1 relative flex items-center justify-center pb-10 md:pb-0">
       <img :src="'http://localhost:8000/storage/' + userGroup[currentIndex].media_path" class="w-full h-full object-contain md:max-w-md" />

       <!-- Zonas de clique transparentes (Avança por toque) -->
       <div @click="prevStory" class="absolute top-0 left-0 w-1/3 h-full cursor-pointer z-10"></div>
       <div @click="nextStory" class="absolute top-0 right-0 w-2/3 h-full cursor-pointer z-10"></div>
    </div>
  </div>
</template>