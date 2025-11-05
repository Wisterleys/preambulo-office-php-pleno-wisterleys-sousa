<template>
  <div class="minhas-locacoes-container">
    <div class="container">
      <h1 class="locacoes-title">Minhas Locações</h1>

      <div v-if="isLoading" class="loading">
        <p>Carregando locações...</p>
      </div>

      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <div v-else-if="locacoes.length > 0" class="locacoes-list">
        <div v-for="locacao in locacoes" :key="locacao.id" class="locacao-card">
          <div class="locacao-header">
            <h3 class="locacao-id">Locação #{{ locacao.uuid }}</h3>
            <span :class="['locacao-status', `status-${locacao.status}`]">
              {{ formatStatus(locacao.status) }}
            </span>
          </div>

          <div class="locacao-info">
            <div class="info-item">
              <span class="info-label">Data de Início:</span>
              <span class="info-value">{{ formatDate(locacao.data_inicio) }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Data de Fim:</span>
              <span class="info-value">{{ formatDate(locacao.data_fim) }}</span>
            </div>
            <div v-if="locacao.data_devolucao" class="info-item">
              <span class="info-label">Data de Devolução:</span>
              <span class="info-value">{{ formatDate(locacao.data_devolucao) }}</span>
            </div>
          </div>

          <div class="filmes-list">
            <h4 class="filmes-title">Filmes:</h4>
            <ul class="filmes">
              <li v-for="filme in locacao.filmes" :key="filme.id" class="filme-item">
                {{ filme.titulo }}
              </li>
            </ul>
          </div>

          <div v-if="locacao.status === 'ativa'" class="locacao-actions">
            <button @click="devolverLocacao(locacao.id)" class="btn btn-primary">
              Devolver Filmes
            </button>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <p>Você não tem nenhuma locação no momento</p>
        <router-link to="/filmes" class="btn btn-primary">Explorar Catálogo</router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useLocacaoStore } from '../store/locacaoStore';

const locacaoStore = useLocacaoStore();
const locacoes = ref<any[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);

onMounted(async () => {
  isLoading.value = true;
  try {
    await locacaoStore.loadMinhasLocacoes();
    locacoes.value = locacaoStore.minhasLocacoes;
  } catch (err: any) {
    error.value = 'Erro ao carregar locações';
  } finally {
    isLoading.value = false;
  }
});

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('pt-BR');
};

const formatStatus = (status: string): string => {
  const statusMap: Record<string, string> = {
    ativa: 'Ativa',
    devolvida: 'Devolvida',
    atrasada: 'Atrasada',
  };
  return statusMap[status] || status;
};

const devolverLocacao = async (id: string) => {
  try {
    await locacaoStore.devolverLocacao(id);
    locacoes.value = locacaoStore.minhasLocacoes;
  } catch (err: any) {
    error.value = 'Erro ao devolver locação';
  }
};
</script>

<style scoped>
.minhas-locacoes-container {
  padding: var(--spacing-2xl) 0;
}

.locacoes-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--spacing-lg);
  text-transform: uppercase;
}

.loading,
.empty-state {
  text-align: center;
  padding: var(--spacing-2xl);
  color: var(--color-text);
  font-size: var(--font-size-lg);
}

.alert {
  padding: var(--spacing-lg);
  border-radius: var(--border-radius);
  margin-bottom: var(--spacing-lg);
}

.alert-danger {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid var(--color-danger);
  color: var(--color-danger);
}

.locacoes-list {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-lg);
}

.locacao-card {
  background-color: var(--color-secondary);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-lg);
  transition: all var(--transition-base);
}

.locacao-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-lg);
}

.locacao-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-lg);
  border-bottom: 1px solid var(--color-border);
}

.locacao-id {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-light);
}

.locacao-status {
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--border-radius);
  font-size: var(--font-size-sm);
  font-weight: 600;
}

.status-ativa {
  background-color: rgba(16, 185, 129, 0.1);
  color: var(--color-success);
  border: 1px solid var(--color-success);
}

.status-devolvida {
  background-color: rgba(59, 130, 246, 0.1);
  color: var(--color-info);
  border: 1px solid var(--color-info);
}

.status-atrasada {
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--color-danger);
  border: 1px solid var(--color-danger);
}

.locacao-info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-lg);
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

.info-label {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text);
}

.info-value {
  font-size: var(--font-size-base);
  color: var(--color-light);
}

.filmes-list {
  margin-bottom: var(--spacing-lg);
}

.filmes-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--color-light);
  margin-bottom: var(--spacing-sm);
}

.filmes {
  list-style: none;
  padding: 0;
}

.filme-item {
  padding: var(--spacing-sm) 0;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-border);
}

.filme-item:last-child {
  border-bottom: none;
}

.locacao-actions {
  display: flex;
  gap: var(--spacing-md);
}
</style>
