FROM node:20-alpine

WORKDIR /app

# Copia os arquivos de dependências primeiro (otimização de cache do Docker)
COPY package*.json ./

# Instala as dependências
RUN npm install

# Copia o restante do código
COPY . .

# Expõe a porta padrão do Vite
EXPOSE 5173

# Comando para rodar o Vite expondo para a rede externa do Docker
CMD ["npm", "run", "dev", "--", "--host"]