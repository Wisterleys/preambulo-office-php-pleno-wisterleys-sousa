FROM node:22-alpine

# Instalar pnpm
RUN corepack enable && corepack prepare pnpm@latest --activate

# Configurar diretório de trabalho
WORKDIR /app

# Copiar arquivos de dependências
COPY package.json pnpm-lock.yaml* ./

# Instalar dependências
RUN pnpm install --frozen-lockfile

# Copiar código fonte
COPY . .

# Expor porta
EXPOSE 3000

# Comando para desenvolvimento
CMD ["pnpm", "run", "dev", "--host"]