# Préambulo Movies

Sistema completo de locação de filmes com backend Laravel 12 (API RESTful) e frontend Vue 3, inspirado no design do Prime Video.

## Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **PostgreSQL** - Banco de dados relacional
- **Redis** - Cache e filas
- **JWT** - Autenticação stateless
- **Docker** - Containerização
- **PHPUnit** - Testes automatizados

### Frontend
- **Vue 3** - Framework JavaScript progressivo
- **Vite** - Build tool moderna
- **Pinia** - Gerenciamento de estado
- **Vue Router** - Roteamento SPA
- **Axios** - Cliente HTTP

## Funcionalidades

### Autenticação e Autorização
O sistema implementa autenticação via JWT com três níveis de acesso baseados em roles:

- **Admin**: Acesso total ao sistema, incluindo criação e gestão de pessoas, filmes e locações
- **Atendente**: Pode criar clientes e gerenciar locações
- **Cliente**: Visualiza o catálogo e suas próprias locações

### Gestão de Pessoas
Cada pessoa está vinculada a um usuário do sistema através de relacionamento 1:1. O sistema cria automaticamente o usuário ao cadastrar uma pessoa via Observer pattern. Suporta upload e crop de foto de perfil, validação de CPF e verificação de maioridade para locações.

### Catálogo de Filmes
O catálogo é público e otimizado com cache Redis. Administradores podem realizar CRUD completo de filmes, incluindo controle de quantidade disponível, categorização e upload de imagens.

### Sistema de Locações
Implementa regras de negócio complexas incluindo:
- Validação de maioridade para alugar filmes
- Controle automático de disponibilidade
- Cálculo de multas (R$ 5,00 por dia de atraso por filme)
- Relacionamento N:N com filmes através de tabela pivot
- Notificações de atraso via jobs em fila

## Arquitetura

O projeto segue princípios de **Clean Architecture** com separação clara de responsabilidades:

### Camadas do Backend
```
Migrations → Models → Observers → Repositories (Interfaces) → DAOs (Eloquent) → Controllers → Endpoints
```

### Repository Pattern
Todas as operações de dados passam por interfaces, permitindo fácil substituição de implementações e facilitando testes. Os DAOs implementam as interfaces usando Eloquent ORM.

### Observers
Automatizam tarefas como criação de usuários, invalidação de cache e atualização de status de locações atrasadas.

## Docker

O projeto utiliza Docker Compose com os seguintes serviços:

- **postgres**: Banco de dados PostgreSQL 15
- **redis**: Cache e gerenciamento de filas
- **backend**: PHP-FPM 8.1 com Laravel
- **nginx**: Servidor web para a API
- **queue**: Worker para processar jobs em background
- **frontend**: Servidor de desenvolvimento Vue 3

## Instalação

### Pré-requisitos
- Docker e Docker Compose instalados
- Git

### Passo a Passo

1. **Clone o repositório**
```bash
git clone <repository-url>
cd preambulo-movies
```

2. **Configure as variáveis de ambiente**
```bash
cd backend
cp .env.example .env
# Edite o .env conforme necessário
```

3. **Suba os containers**
```bash
docker-compose up -d

docker-compose exec backend composer install

docker-compose exec backend php artisan key:generate

docker compose exec backend php artisan jwt:secret
```

4. **Execute as migrations**
```bash
docker-compose exec backend php artisan migrate
```

5. **Gere dados de exemplo (opcional)**
```bash
docker-compose exec backend php artisan db:seed
```

6. **Acesse a aplicação**
- API: http://localhost:8000
- Frontend: http://localhost:3000

## Endpoints da API

### Autenticação
- `POST /api/login` - Login e geração de token
- `POST /api/logout` - Logout
- `POST /api/refresh` - Renovar token
- `GET /api/me` - Dados do usuário autenticado

### Filmes (Público)
- `GET /api/filmes` - Listar catálogo
- `GET /api/filmes/{uuid}` - Detalhes do filme

### Perfil (Autenticado)
- `GET /api/perfil` - Dados do perfil
- `PUT /api/perfil` - Atualizar perfil
- `PUT /api/perfil/senha` - Alterar senha
- `POST /api/perfil/foto` - Upload de foto

### Pessoas (Admin)
- `GET /api/pessoas` - Listar
- `POST /api/pessoas` - Criar
- `GET /api/pessoas/{uuid}` - Ver detalhes
- `PUT /api/pessoas/{uuid}` - Atualizar
- `DELETE /api/pessoas/{uuid}` - Remover

### Filmes (Admin)
- `POST /api/filmes` - Criar
- `PUT /api/filmes/{uuid}` - Atualizar
- `DELETE /api/filmes/{uuid}` - Remover

### Locações (Admin/Atendente)
- `GET /api/locacoes` - Listar todas
- `POST /api/locacoes` - Criar locação
- `GET /api/locacoes/{uuid}` - Ver detalhes
- `PUT /api/locacoes/{uuid}/devolver` - Devolver filmes

### Locações (Cliente)
- `GET /api/minhas-locacoes` - Ver minhas locações

## Testes

Execute os testes automatizados:

```bash
docker-compose exec backend php artisan test
```

Os testes cobrem:
- DAOs e Repositories
- Controllers e endpoints
- Autenticação e autorização (RBAC)
- Upload de fotos
- Regras de negócio de locações
- Cálculo de multas

## Modelo de Dados

### Entidades Principais

**users** (Laravel padrão)
- Não alterada, mantém estrutura original

**pessoas**
- Relacionamento 1:1 com users
- Campos: uuid, nome_completo, cpf, data_nascimento, endereco, foto_perfil, role
- Roles: admin, attendant, customer

**filmes**
- Campos: uuid, titulo, sinopse, ano, categoria, valor_locacao, quantidade_disponivel, imagem_path

**locacoes**
- Campos: uuid, pessoa_id, data_inicio, data_prevista_devolucao, data_devolucao, status, valor_total, multa_total
- Status: ativa, devolvida, atrasada, cancelada

**locacao_filme** (pivot)
- Relacionamento N:N entre locacoes e filmes
- Campos: locacao_id, filme_id, quantidade, valor_unitario

## Frontend

O frontend Vue 3 apresenta design inspirado no Prime Video com:

- **Layout responsivo** com grid de filmes
- **Navegação por categorias** e busca
- **Dashboard personalizado** por role
- **Componente de crop de imagem** reutilizável
- **Gestão completa** de pessoas, filmes e locações
- **Tema escuro** com cores da identidade Préambulo Movies

## Segurança

- Autenticação JWT stateless
- Middleware RBAC para controle de acesso
- Validação de dados em FormRequests
- Sanitização de inputs
- Proteção contra SQL Injection via Eloquent
- CORS configurado
- Senhas hasheadas com bcrypt

## Convenções

### Código
- Clean Code principles
- Repository Pattern
- Observer Pattern
- Dependency Injection

### Git
- Commits semânticos (feat, fix, docs, refactor, test, chore)
- Branches descritivas
- Pull requests com revisão

### Mensagens
- Todas as mensagens e logs em português brasileiro
- Respostas da API padronizadas com `sucesso`, `mensagem`, `dados`
