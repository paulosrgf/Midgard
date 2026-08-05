import axios from 'axios'

const api = axios.create({
  // URL base do seu container Laravel
  baseURL: 'http://localhost:8000/api',
})

// O Interceptor: Antes de qualquer requisição sair, ele cola o token no cabeçalho
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api
