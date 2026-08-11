<script setup>
import { ref } from 'vue'

const emit = defineEmits(['close', 'submit'])

const caption = ref('')
const imagePreview = ref(null)
const selectedFile = ref(null)
const fileInput = ref(null)

const handleImageSelected = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const triggerFileInput = () => {
  fileInput.value.click()
}

const fecharModal = () => {
  imagePreview.value = null
  selectedFile.value = null
  caption.value = ''
  emit('close')
}

const publicarPost = () => {
  if (!selectedFile.value) {
    alert('Selecione uma imagem para o post!')
    return
  }

  // Emite explicitamente o arquivo e a legenda
  emit('submit', {
    caption: caption.value,
    image: selectedFile.value,
  })
}
</script>

<template>
  <div
    class="fixed inset-0 bg-black/80 z-[60] flex justify-center items-center p-4 backdrop-blur-sm"
  >
    <div
      class="bg-zinc-950 border border-zinc-800 rounded-xl w-full max-w-lg overflow-hidden flex flex-col shadow-2xl"
    >
      <!-- Cabeçalho -->
      <div class="flex items-center justify-between p-4 border-b border-zinc-800">
        <button @click="fecharModal" class="text-zinc-400 hover:text-white transition-colors">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="currentColor"
            viewBox="0 0 16 16"
          >
            <path
              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
            />
          </svg>
        </button>
        <h2 class="font-semibold text-white">Criar nova publicação</h2>
        <button
          @click="publicarPost"
          class="font-bold text-indigo-400 hover:text-indigo-300 transition-colors"
        >
          Compartilhar
        </button>
      </div>

      <!-- Corpo -->
      <div class="flex flex-col p-4 gap-4">
        <div
          class="w-full aspect-square md:aspect-[4/5] bg-zinc-900 border-2 border-dashed border-zinc-800 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-zinc-700 transition-colors overflow-hidden relative"
          @click="triggerFileInput"
        >
          <img
            v-if="imagePreview"
            :src="imagePreview"
            class="w-full h-full object-cover absolute inset-0"
            alt="Preview"
          />

          <div v-else class="flex flex-col items-center text-zinc-500">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="48"
              height="48"
              fill="currentColor"
              class="mb-2"
              viewBox="0 0 16 16"
            >
              <path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
              <path
                d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2zM14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1zM2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1h-10z"
              />
            </svg>
            <span class="font-medium">Selecionar imagem</span>
          </div>

          <input
            type="file"
            ref="fileInput"
            accept="image/*"
            class="hidden"
            @change="handleImageSelected"
          />
        </div>

        <div class="flex items-start gap-3">
          <textarea
            v-model="caption"
            placeholder="Escreva uma legenda..."
            class="w-full bg-transparent text-white resize-none outline-none min-h-[80px]"
          ></textarea>
        </div>
      </div>
    </div>
  </div>
</template>
