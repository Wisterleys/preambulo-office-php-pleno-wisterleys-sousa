import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { locacaoService, Locacao } from '../services/LocacaoService';

export const useLocacaoStore = defineStore('locacao', () => {
  const locacoes = ref<Locacao[]>([]);
  const minhasLocacoes = ref<Locacao[]>([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const getLocacoes = computed(() => locacoes.value);
  const getMinhasLocacoes = computed(() => minhasLocacoes.value);

  const loadLocacoes = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      locacoes.value = await locacaoService.getAllLocacoes();
    } catch (err: any) {
      error.value = err.message || 'Erro ao carregar locações';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const loadMinhasLocacoes = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      minhasLocacoes.value = await locacaoService.getMinhasLocacoes();
    } catch (err: any) {
      error.value = err.message || 'Erro ao carregar minhas locações';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const createLocacao = async (locacao: Partial<Locacao>): Promise<Locacao> => {
    try {
      const newLocacao = await locacaoService.createLocacao(locacao);
      locacoes.value.push(newLocacao);
      return newLocacao;
    } catch (err: any) {
      error.value = err.message || 'Erro ao criar locação';
      throw err;
    }
  };

  const devolverLocacao = async (id: string): Promise<Locacao> => {
    try {
      const updatedLocacao = await locacaoService.devolverLocacao(id);
      const index = locacoes.value.findIndex((l) => l.id === id);
      if (index !== -1) {
        locacoes.value[index] = updatedLocacao;
      }
      const minhaIndex = minhasLocacoes.value.findIndex((l) => l.id === id);
      if (minhaIndex !== -1) {
        minhasLocacoes.value[minhaIndex] = updatedLocacao;
      }
      return updatedLocacao;
    } catch (err: any) {
      error.value = err.message || 'Erro ao devolver locação';
      throw err;
    }
  };

  return {
    locacoes: getLocacoes,
    minhasLocacoes: getMinhasLocacoes,
    isLoading,
    error,
    loadLocacoes,
    loadMinhasLocacoes,
    createLocacao,
    devolverLocacao,
  };
});
