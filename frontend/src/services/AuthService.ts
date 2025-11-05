import { ApiCore } from './core/ApiCore';

export interface LoginRequest {
  email: string;
  password: string;
}

export interface AuthResponse {
  sucesso: boolean;
  token: string;
  tipo_token: string;
  expira_em: number;
  usuario: {
    nome: string;
    email: string;
    role: string;
    uuid_pessoa: string | null;
  };
}

export interface UserData {
  sucesso: boolean;
  dados: {
    user: any;
    pessoa: any;
  };
}

export class AuthService extends ApiCore {
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    return this.post<AuthResponse>('/login', credentials);
  }

  async logout(): Promise<void> {
    await this.post('/logout', {});
  }

  async me(): Promise<UserData> {
    return this.get<UserData>('/me');
  }

  async refresh(): Promise<AuthResponse> {
    return this.post<AuthResponse>('/refresh', {});
  }
}

export const authService = new AuthService();
