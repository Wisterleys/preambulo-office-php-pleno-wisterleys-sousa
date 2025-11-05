import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { filmeService, Filme } from '../services/FilmeService';

export const useFilmeStore = defineStore('filme', () => {
  const filmes = ref<Filme[]>([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const getFilmes = computed(() => filmes.value);

  const loadFilmes = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      filmes.value = await filmeService.getAllFilmes();
    } catch (err: any) {
      error.value = err.message || 'Erro ao carregar filmes';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const getFilmeById = async (id: string): Promise<Filme> => {
    try {
      return await filmeService.getFilmeById(id);
    } catch (err: any) {
      error.value = err.message || 'Erro ao carregar filme';
      throw err;
    }
  };

  const createFilme = async (filme: Partial<Filme>): Promise<Filme> => {
    try {
      const newFilme = await filmeService.createFilme(filme);
      filmes.value.push(newFilme);
      return newFilme;
    } catch (err: any) {
      error.value = err.message || 'Erro ao criar filme';
      throw err;
    }
  };

  const updateFilme = async (id: string, filme: Partial<Filme>): Promise<Filme> => {
    try {
      const updatedFilme = await filmeService.updateFilme(id, filme);
      const index = filmes.value.findIndex((f) => f.id === id);
      if (index !== -1) {
        filmes.value[index] = updatedFilme;
      }
      return updatedFilme;
    } catch (err: any) {
      error.value = err.message || 'Erro ao atualizar filme';
      throw err;
    }
  };

  const deleteFilme = async (id: string) => {
    try {
      await filmeService.deleteFilme(id);
      filmes.value = filmes.value.filter((f) => f.id !== id);
    } catch (err: any) {
      error.value = err.message || 'Erro ao excluir filme';
      throw err;
    }
  };

  return {
    filmes: getFilmes,
    isLoading,
    error,
    loadFilmes,
    getFilmeById,
    createFilme,
    updateFilme,
    deleteFilme,
  };
});
