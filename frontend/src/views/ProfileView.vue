<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const authStore = useAuthStore()
const posts = ref([])
const isEditing = ref(false)
const isSaving = ref(false)

// Estado do formulário de edição
const editForm = ref({
  name: authStore.user?.name || '',
  username: authStore.user?.username || '',
  avatar: null,
})

const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'
const storageBaseUrl = apiUrl.replace('/api', '/storage/')

const fetchMyPosts = async () => {
  try {
    const response = await api.get(`/users/${authStore.user.id}/posts`)
    posts.value = response.data
  } catch (error) {
    console.error('Erro ao buscar sagas:', error)
  }
}

const handleAvatarUpload = (event) => {
  editForm.value.avatar = event.target.files[0]
}

const saveProfile = async () => {
  isSaving.value = true
  try {
    const formData = new FormData()

    // O Laravel precisa do _method PUT quando mandamos arquivos via POST
    formData.append('_method', 'PUT')
    formData.append('name', editForm.value.name)
    formData.append('username', editForm.value.username)

    if (editForm.value.avatar) {
      formData.append('avatar', editForm.value.avatar)
    }

    const response = await api.post(`/users/${authStore.user.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    // Atualiza a store e o localStorage instantaneamente
    authStore.setUser(response.data)
    isEditing.value = false

    // Recarrega os posts
    fetchMyPosts()
  } catch (error) {
    console.error('Erro ao salvar perfil', error)
    alert('Erro ao atualizar as runas. Verifique se o nome já existe.')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchMyPosts()
})
</script>

<template>
  <main
    class="flex-1 flex justify-center w-full max-w-[720px] mx-auto pb-24 md:pb-0 bg-black border-r border-zinc-900 relative"
  >
    <!-- MODAL DE EDIÇÃO -->
    <div
      v-if="isEditing"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm p-4"
    >
      <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-xl w-full max-w-md shadow-2xl">
        <h3
          class="text-xl font-serif text-white uppercase tracking-widest mb-6 border-b border-zinc-800 pb-4"
        >
          Forjar Identidade
        </h3>

        <form @submit.prevent="saveProfile" class="flex flex-col gap-5">
          <!-- Input Avatar -->
          <div class="flex flex-col gap-2">
            <label class="text-[10px] text-zinc-500 uppercase tracking-widest">Novo Avatar</label>
            <input
              type="file"
              @change="handleAvatarUpload"
              accept="image/*"
              class="text-zinc-300 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-zinc-800 file:text-white hover:file:bg-zinc-700 transition-all cursor-pointer"
            />
          </div>

          <!-- Input Nome -->
          <div class="flex flex-col gap-2">
            <label class="text-[10px] text-zinc-500 uppercase tracking-widest">Nome Completo</label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="bg-zinc-900 border border-zinc-700 text-white p-3 rounded text-sm focus:border-red-600 focus:outline-none"
            />
          </div>

          <!-- Input Username -->
          <div class="flex flex-col gap-2">
            <label class="text-[10px] text-zinc-500 uppercase tracking-widest"
              >Nome de Guerra (@)</label
            >
            <input
              v-model="editForm.username"
              type="text"
              required
              class="bg-zinc-900 border border-zinc-700 text-white p-3 rounded text-sm focus:border-red-600 focus:outline-none"
            />
          </div>

          <!-- Botões -->
          <div class="flex justify-end gap-3 mt-4">
            <button
              type="button"
              @click="isEditing = false"
              class="text-zinc-400 hover:text-white px-4 py-2 uppercase tracking-widest text-[10px] font-bold transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSaving"
              class="bg-zinc-100 hover:bg-red-700 hover:text-white text-black px-6 py-3 rounded uppercase tracking-widest text-[10px] font-bold transition-all disabled:opacity-50"
            >
              {{ isSaving ? 'Gravando...' : 'Salvar Alterações' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- CORPO DO PERFIL -->
    <div class="w-full flex flex-col">
      <!-- O Banner (Mantido como estético) -->
      <div class="relative w-full h-48 bg-zinc-950 border-b border-zinc-900 overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2000&auto=format&fit=crop"
          class="w-full h-full object-cover grayscale opacity-30 mix-blend-overlay"
        />
      </div>

      <div class="px-4 md:px-8 pb-10 relative">
        <div class="flex justify-between items-end -mt-16 mb-6">
          <div
            class="w-32 h-32 bg-black border-4 border-zinc-900 relative z-10 overflow-hidden rounded-xl shadow-lg"
          >
            <!-- Correção: Verificando a string do avatar do usuário logado -->
            <img
              :src="
                authStore.user?.avatar
                  ? authStore.user.avatar.startsWith('http')
                    ? authStore.user.avatar
                    : storageBaseUrl + authStore.user.avatar
                  : 'https://ui-avatars.com/api/?name=' +
                    (authStore.user?.username || 'Guerreiro') +
                    '&background=18181b&color=ef4444&size=200&bold=true'
              "
              class="w-full h-full object-cover"
            />
          </div>
          <button
            @click="isEditing = true"
            class="border border-zinc-700 text-zinc-300 hover:text-white hover:border-zinc-400 font-sans text-[10px] uppercase tracking-widest px-6 py-3 transition-colors bg-black rounded"
          >
            Editar Perfil
          </button>
        </div>

        <h1 class="font-serif text-3xl uppercase tracking-widest text-white mb-2">
          {{ authStore.user?.name || authStore.user?.username }}
        </h1>
        <p class="font-sans text-sm text-zinc-500">@{{ authStore.user?.username }}</p>

        <div class="flex gap-8 mt-8 pt-6 border-t border-zinc-900">
          <div class="flex flex-col text-center">
            <span class="font-serif text-2xl text-zinc-100">{{ posts.length }}</span>
            <span class="font-sans text-[10px] uppercase tracking-widest text-zinc-600 mt-1"
              >Sagas</span
            >
          </div>
        </div>
      </div>

      <!-- NOVA ÁREA DE SAGAS: GRID ESTILO INSTAGRAM -->
      <div class="w-full bg-black">
        <div class="border-y border-zinc-900 p-4 flex justify-center gap-12">
          <span
            class="font-serif text-xs tracking-widest uppercase text-white border-b border-white pb-1 cursor-pointer"
            >Grade</span
          >
        </div>

        <div
          v-if="posts.length === 0"
          class="p-16 text-center text-zinc-600 font-sans text-sm tracking-widest uppercase"
        >
          Sua espada ainda está limpa. Nenhuma saga registrada.
        </div>

        <!-- Mosaico 3x3 -->
        <div v-else class="grid grid-cols-3 gap-1 bg-black">
          <div
            v-for="post in posts"
            :key="post.id"
            class="aspect-square bg-zinc-900 relative group cursor-pointer overflow-hidden border border-zinc-900"
          >
            <img
              :src="
                post.image_path
                  ? post.image_path.startsWith('http')
                    ? post.image_path
                    : storageBaseUrl + post.image_path
                  : ''
              "
              class="w-full h-full object-cover group-hover:opacity-70 transition-opacity duration-300"
              alt="Saga"
            />
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
