import { inject, Injectable } from '@angular/core';
import { ApiService } from '../../core/services/api.service';
import { AuthTokens } from '../entity/tokens.dto';
import { TokenStorageService } from '../../core/services/token-storage.service';
import { LoginResponseDto } from '../entity/login-response.dto';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly api = inject(ApiService);
  private readonly tokenStorage = inject(TokenStorageService);

  async login(payload: { email: string; password: string }): Promise<LoginResponseDto> {
    const res = await this.api.post<any>('/auth/login', payload);

    const loginResponse: LoginResponseDto = {
      accessToken: res.access_token,
      refreshToken: res.refresh_token,
      user: res.user,
    };

    this.tokenStorage.saveTokens(loginResponse.accessToken, loginResponse.refreshToken);
    return loginResponse;
  }

  async refresh(refreshToken: string): Promise<AuthTokens> {
    const res = await this.api.post<any>('/auth/token/refresh', {
      refresh_token: refreshToken,
    });

    return {
      accessToken: res.access_token,
      refreshToken: res.refresh_token,
    };
  }

  register(payload: { email: string; username: string; password: string }): Promise<string> {
    return this.api.post<string>('/auth/register', payload);
  }

  logout() {
    this.tokenStorage.clear();
  }
}
