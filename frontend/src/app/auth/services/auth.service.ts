import {inject, Injectable, signal} from '@angular/core';
import {ApiService} from '../../core/services/api.service';
import {AuthTokens} from '../entity/tokens.dto';
import {TokenStorageService} from '../../core/services/token-storage.service';
import {LoginResponseDto} from '../entity/login-response.dto';
import {User} from "../entity/user.entity";

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly api = inject(ApiService);
  private readonly tokenStorage = inject(TokenStorageService);
  private readonly _loggedInUser$ = signal<User | null>(null);
  readonly loggedInUser$ = this._loggedInUser$.asReadonly();

  async login(payload: { email: string; password: string }): Promise<LoginResponseDto> {
    const res = await this.api.post<any>('/auth/login', payload);

    const loginResponse: LoginResponseDto = {
      accessToken: res.access_token,
      refreshToken: res.refresh_token,
      user: res.user,
    };

    this.tokenStorage.saveTokens(loginResponse.accessToken, loginResponse.refreshToken);
    this._loggedInUser$.set(loginResponse.user);
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

  async loadCurrentUser(): Promise<void> {
    const token = this.tokenStorage.getAccessToken();
    if (!token) return;

    try {
      const user = await this.api.get<User>('/users/me');
      this._loggedInUser$.set(user);
    } catch {
      this.logout();
    }
  }

  logout() {
    this.tokenStorage.clear();
  }
}
