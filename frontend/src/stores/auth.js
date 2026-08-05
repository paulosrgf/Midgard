import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // Tenta pegar o token do navegador assim que a aplicação abre
    token: localStorage.getItem('token') || null,
    user: null,
  }),

  actions: {
    setToken(tokenValue) {
      this.token = tokenValue
      localStorage.setItem('token', tokenValue)
    },

    // Função para sair da conta
    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token') // Apaga o crachá do navegador
    },
  },
})
