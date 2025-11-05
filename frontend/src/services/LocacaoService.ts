import { ApiCore } from './core/ApiCore';

export interface Locacao {
  id: string;
  uuid: string;
  pessoa_id: string;
  data_inicio: string;
  data_fim: string;
  data_devolucao?: string;
  status: 'ativa' | 'devolvida' | 'atrasada';
  filmes: any[];
  created_at: string;
  updated_at: string;
}

export interface LocacaoResponse {
  sucesso: boolean;
  dados: Locacao | Locacao[];
  mensagem?: string;
}

export class LocacaoService extends ApiCore {
  async getAllLocacoes(): Promise<Locacao[]> {
    const response = await this.get<LocacaoResponse>('/locacoes');
    return Array.isArray(response.dados) ? response.dados : [response.dados];
  }

  async getLocacaoById(id: string): Promise<Locacao> {
    const response = await this.get<LocacaoResponse>(`/locacoes/${id}`);
    return response.dados as Locacao;
  }

  async createLocacao(locacao: Partial<Locacao>): Promise<Locacao> {
    const response = await this.post<LocacaoResponse>('/locacoes', locacao);
    return response.dados as Locacao;
  }

  async getMinhasLocacoes(): Promise<Locacao[]> {
    const response = await this.get<LocacaoResponse>('/minhas-locacoes');
    return Array.isArray(response.dados) ? response.dados : [response.dados];
  }

  async devolverLocacao(id: string): Promise<Locacao> {
    const response = await this.put<LocacaoResponse>(`/locacoes/${id}/devolver`, {});
    return response.dados as Locacao;
  }
}

export const locacaoService = new LocacaoService();
