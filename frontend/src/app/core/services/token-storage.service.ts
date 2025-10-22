import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class TokenStorageService {
  private readonly _isLoggedIn = signal(this.hasValidAccessToken());
  readonly isLoggedIn = this._isLoggedIn.asReadonly();

  saveTokens(access: string, refresh?: string): void {
    localStorage.setItem('access_token', access);
    if (refresh) {
      localStorage.setItem('refresh_token', refresh);
    }
    this._isLoggedIn.set(true);
  }

  getAccessToken(): string | null {
    return localStorage.getItem('access_token');
  }

  getRefreshToken(): string | null {
    return localStorage.getItem('refresh_token');
  }

  clear(): void {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    this._isLoggedIn.set(false);
  }

  private hasValidAccessToken(): boolean {
    const token = localStorage.getItem('access_token');
    return !!token && token.length > 20
  }
}
