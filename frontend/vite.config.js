import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite' // 1. Importe o plugin aqui

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(), // 2. Registre o plugin aqui
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
})
