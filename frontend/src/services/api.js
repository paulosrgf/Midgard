import axios from 'axios'

const api = axios.create({
  // O Vite expõe as variáveis de ambiente usando import.meta.env
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
})

export default api
