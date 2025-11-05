import { ApiCore } from './core/ApiCore';

export interface Filme {
  id: string;
  uuid: string;
  titulo: string;
  descricao: string;
  genero: string;
  ano: number;
  duracao: number;
  diretor: string;
  poster_url?: string;
  created_at: string;
  updated_at: string;
}

export interface FilmeResponse {
  sucesso: boolean;
  dados: Filme | Filme[];
  mensagem?: string;
}

export class FilmeService extends ApiCore {
  async getAllFilmes(): Promise<Filme[]> {
    const response = await this.get<FilmeResponse>('/filmes');
    return Array.isArray(response.dados) ? response.dados : [response.dados];
  }

  async getFilmeById(id: string): Promise<Filme> {
    const response = await this.get<FilmeResponse>(`/filmes/${id}`);
    return response.dados as Filme;
  }

  async createFilme(filme: Partial<Filme>): Promise<Filme> {
    const response = await this.post<FilmeResponse>('/filmes', filme);
    return response.dados as Filme;
  }

  async updateFilme(id: string, filme: Partial<Filme>): Promise<Filme> {
    const response = await this.put<FilmeResponse>(`/filmes/${id}`, filme);
    return response.dados as Filme;
  }

  async deleteFilme(id: string): Promise<void> {
    await this.delete<FilmeResponse>(`/filmes/${id}`);
  }
}

export const filmeService = new FilmeService();
