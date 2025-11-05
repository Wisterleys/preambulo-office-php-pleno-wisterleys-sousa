# Préâmbulo Movies Frontend - TODO

## Estrutura Base
- [x] Inicializar projeto Vue 3 + Vite
- [x] Configurar Pinia para gerenciamento de estado
- [x] Configurar Vue Router para roteamento
- [x] Configurar Axios para requisições HTTP
- [x] Criar estrutura de diretórios
- [x] Criar arquivo CSS principal com design system
- [x] Criar arquivo main.ts e App.vue

## Serviços
- [x] Criar ApiCore para centralizar requisições HTTP
- [x] Criar AuthService para autenticação
- [x] Criar FilmeService para gerenciar filmes
- [x] Criar PessoaService para gerenciar pessoas
- [x] Criar LocacaoService para gerenciar locações
- [x] Criar PerfilService para gerenciar perfil do usuário

## Store (Pinia)
- [x] Criar authStore para gerenciar estado de autenticação
- [x] Criar filmeStore para gerenciar estado de filmes
- [x] Criar locacaoStore para gerenciar estado de locações
- [ ] Criar perfilStore para gerenciar estado do perfil

## Roteamento
- [x] Configurar router com rotas básicas
- [x] Implementar proteção de rotas autenticadas
- [ ] Implementar redirecionamento após login

## Views
- [x] Criar Home.vue com catálogo de filmes em destaque
- [x] Criar Login.vue para autenticação
- [x] Criar Dashboard.vue para usuários autenticados
- [x] Criar CatalogueFilmes.vue com busca e filtros
- [x] Criar NotFound.vue para página 404
- [x] Criar Perfil.vue para editar perfil do usuário
- [x] Criar MinhasLocacoes.vue para visualizar locações do usuário
- [ ] Criar Cadastro.vue para registro de novos usuários

## Componentes
- [ ] Criar componente NavBar reutilizável
- [ ] Criar componente FilmeCard reutilizável
- [ ] Criar componente Modal para confirmações
- [ ] Criar componente Loading para estados de carregamento
- [ ] Criar componente Alert para mensagens
- [ ] Criar componente Pagination para paginação

## Integração com Backend
- [ ] Testar conexão com API do backend
- [ ] Implementar tratamento de erros de API
- [ ] Implementar refresh de token JWT
- [ ] Implementar logout automático ao expirar token
- [ ] Implementar persistência de sessão

## Design e UX
- [x] Implementar design system com CSS puro
- [x] Implementar tema escuro (azul-escuro, preto, cinza metálico)
- [x] Implementar tipografia moderna (Poppins)
- [x] Implementar animações suaves
- [ ] Implementar responsividade completa
- [ ] Implementar acessibilidade (WCAG)
- [ ] Implementar micro-interações

## Testes
- [ ] Criar testes unitários para serviços
- [ ] Criar testes unitários para stores
- [ ] Criar testes de integração para views
- [ ] Criar testes E2E para fluxos principais

## Documentação
- [ ] Documentar estrutura do projeto
- [ ] Documentar como adicionar novos serviços
- [ ] Documentar como adicionar novas views
- [ ] Documentar como adicionar novos componentes

## Deploy
- [ ] Configurar build para produção
- [ ] Configurar variáveis de ambiente
- [ ] Testar build em ambiente de produção
- [ ] Deploy para servidor

## Bugs e Melhorias
- [ ] Corrigir erros de TypeScript
- [ ] Implementar validação de formulários
- [ ] Implementar feedback visual para ações do usuário
- [ ] Implementar cache de dados
