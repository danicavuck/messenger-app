import {inject, Injectable} from '@angular/core';
import {
  HttpClient,
  HttpErrorResponse,
  HttpHeaders,
  HttpParams,
  HttpContext,
} from '@angular/common/http';
import {environment} from '../../environments/environment';
import {catchError, throwError, firstValueFrom} from 'rxjs';

export interface HttpRequestOptions {
  params?: HttpParams | Record<string, string | number | boolean>;
  headers?: HttpHeaders | Record<string, string>;
  context?: HttpContext;
  withCredentials?: boolean;
}

@Injectable({providedIn: 'root'})
export class ApiService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = environment.apiUrl.replace(/\/$/, '');

  private buildUrl(endpoint: string): string {
    return endpoint.startsWith('/')
      ? `${this.baseUrl}${endpoint}`
      : `${this.baseUrl}/${endpoint}`;
  }

  private handleError(error: HttpErrorResponse): never {
    const message =
      error.error?.detail ||
      error.error?.message ||
      error.statusText ||
      `Unexpected HTTP error (${error.status})`;
    console.error(`[API ${error.status}] ${message}`, error);
    throw error;
  }

  /** Helper function - request any method */
  private async request<T>(
    method: 'GET' | 'POST' | 'PATCH' | 'DELETE',
    endpoint: string,
    body?: unknown,
    options: HttpRequestOptions = {}
  ): Promise<T> {
    const url = this.buildUrl(endpoint);

    const obs$ = this.http
      .request<T>(method, url, {
        body,
        ...options,
      })
      .pipe(catchError((err) => throwError(() => this.handleError(err))));

    return await firstValueFrom(obs$);
  }

  /** GET */
  get<T>(endpoint: string, options?: HttpRequestOptions): Promise<T> {
    return this.request<T>('GET', endpoint, undefined, options);
  }

    /** POST */
  post<T>(
    endpoint: string,
    body?: unknown,
    options?: HttpRequestOptions
  ): Promise<T> {
    return this.request<T>('POST', endpoint, body, options);
  }

  /** PATCH */
  patch<T>(
    endpoint: string,
    body?: unknown,
    options?: HttpRequestOptions
  ): Promise<T> {
    return this.request<T>('PATCH', endpoint, body, options);
  }

  /** DELETE */
  delete<T>(endpoint: string, options?: HttpRequestOptions): Promise<T> {
    return this.request<T>('DELETE', endpoint, undefined, options);
  }
}
