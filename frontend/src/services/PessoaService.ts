import { ApiCore } from './core/ApiCore';

export interface Pessoa {
  id: string;
  uuid: string;
  nome: string;
  email: string;
  role: 'admin' | 'attendant' | 'customer';
  created_at: string;
  updated_at: string;
}

export interface PessoaResponse {
  sucesso: boolean;
  dados: Pessoa | Pessoa[];
  mensagem?: string;
}

export class PessoaService extends ApiCore {
  async getAllPessoas(): Promise<Pessoa[]> {
    const response = await this.get<PessoaResponse>('/pessoas');
    return Array.isArray(response.dados) ? response.dados : [response.dados];
  }

  async getPessoaById(id: string): Promise<Pessoa> {
    const response = await this.get<PessoaResponse>(`/pessoas/${id}`);
    return response.dados as Pessoa;
  }

  async updatePessoa(id: string, pessoa: Partial<Pessoa>): Promise<Pessoa> {
    const response = await this.put<PessoaResponse>(`/pessoas/${id}`, pessoa);
    return response.dados as Pessoa;
  }

  async deletePessoa(id: string): Promise<void> {
    await this.delete<PessoaResponse>(`/pessoas/${id}`);
  }
}

export const pessoaService = new PessoaService();
