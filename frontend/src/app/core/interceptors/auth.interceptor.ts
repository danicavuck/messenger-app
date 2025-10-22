import {inject} from '@angular/core';
import {
  HttpInterceptorFn,
  HttpRequest,
  HttpHandlerFn,
  HttpErrorResponse,
} from '@angular/common/http';
import {catchError, switchMap, throwError, from, Subject, filter, take, finalize} from 'rxjs';
import {AuthService} from '../../auth/services/auth.service';
import {TokenStorageService} from "../services/token-storage.service";

let isRefreshing = false;
let refreshSubject = new Subject<string>();

export const AuthInterceptor: HttpInterceptorFn = (req, next) => {
  const tokenStorage = inject(TokenStorageService);
  const authService = inject(AuthService);

  const accessToken = tokenStorage.getAccessToken();
  const authReq = accessToken
    ? req.clone({setHeaders: {Authorization: `Bearer ${accessToken}`}})
    : req;

  return next(authReq).pipe(
    catchError((error: HttpErrorResponse) => {
      if (error.status === 401) {
        return handle401(authReq, next, tokenStorage, authService);
      }
      return throwError(() => error);
    })
  );
};

function handle401(
  req: HttpRequest<any>,
  next: HttpHandlerFn,
  tokenStorage: TokenStorageService,
  authService: AuthService
) {
  if (!isRefreshing) {
    isRefreshing = true;
    refreshSubject = new Subject<string>();

    const refreshToken = tokenStorage.getRefreshToken();
    if (!refreshToken) {
      tokenStorage.clear();
      window.location.href = '/login';
      return throwError(() => new Error('No refresh token'));
    }

    return from(authService.refresh(refreshToken)).pipe(
      switchMap((res) => {
        tokenStorage.saveTokens(res.accessToken, res.refreshToken);
        refreshSubject.next(res.accessToken);
        refreshSubject.complete();

        const retried = req.clone({
          setHeaders: {Authorization: `Bearer ${res.accessToken}`},
        });
        return next(retried);
      }),
      catchError((err) => {
        tokenStorage.clear();
        window.location.href = '/login';
        return throwError(() => err);
      }),
      finalize(() => {
        isRefreshing = false;
      })
    );
  } else {
    // Wait for refresh to complete
    return refreshSubject.pipe(
      filter((token) => !!token),
      take(1),
      switchMap((token) =>
        next(req.clone({setHeaders: {Authorization: `Bearer ${token}`}}))
      )
    );
  }
}
