import { ApiCore } from './core/ApiCore';

export interface PerfilData {
  user: {
    id: string;
    name: string;
    email: string;
  };
  pessoa: {
    uuid: string;
    nome: string;
    role: string;
  };
}

export interface PerfilResponse {
  sucesso: boolean;
  dados: PerfilData;
  mensagem?: string;
}

export class PerfilService extends ApiCore {
  async getPerfil(): Promise<PerfilData> {
    const response = await this.get<PerfilResponse>('/perfil');
    return response.dados;
  }

  async updatePerfil(data: Partial<PerfilData>): Promise<PerfilData> {
    const response = await this.put<PerfilResponse>('/perfil', data);
    return response.dados;
  }

  async updatePassword(senhaAtual: string, novaSenha: string): Promise<void> {
    await this.put('/perfil/senha', {
      senha_atual: senhaAtual,
      nova_senha: novaSenha,
    });
  }

  async uploadFoto(file: File): Promise<string> {
    const formData = new FormData();
    formData.append('foto', file);
    const response = await this.client.post('/perfil/foto', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data.url;
  }
}

export const perfilService = new PerfilService();
