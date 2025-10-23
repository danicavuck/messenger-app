import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from "../../auth/services/auth.service";
import {TokenStorageService} from "../services/token-storage.service";

export const authGuard: CanActivateFn = async () => {
  const auth = inject(AuthService);
  const tokenStorage = inject(TokenStorageService);
  const router = inject(Router);

  const token = tokenStorage.getAccessToken();
  if (!token) {
    await router.navigate(['/login']);
    return false;
  }

  const user = auth.loggedInUser$();
  if (!user) {
    await auth.loadCurrentUser();
  }

  if (!auth.loggedInUser$()) {
    await router.navigate(['/login']);
    return false;
  }

  return true;
};
