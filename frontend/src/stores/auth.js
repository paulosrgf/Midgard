import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user = ref(JSON.parse(localStorage.getItem('user')) || null)

  const setToken = (tokenValue) => {
    token.value = tokenValue
    localStorage.setItem('token', tokenValue)
  }

  const setUser = (userValue) => {
    user.value = userValue
    localStorage.setItem('user', JSON.stringify(userValue))
  }

  const login = async (credentials) => {
    const response = await api.post('/login', credentials)
    setToken(response.data.access_token)
    setUser(response.data.user)
    return response.data
  }

  const logout = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return { token, user, setToken, setUser, login, logout }
})
