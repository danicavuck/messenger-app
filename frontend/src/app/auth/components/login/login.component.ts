import {Component, inject, signal} from '@angular/core';
import {FormBuilder, Validators, ReactiveFormsModule} from '@angular/forms';
import {CommonModule} from '@angular/common';
import {AuthService} from '../../services/auth.service';
import {RouterLink} from "@angular/router";

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);

  form = this.fb.group({
    email: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)]],
    password: ['', [Validators.required, Validators.minLength(8)]],
  });

  loading = signal(false);
  showPassword = signal(false);
  successMessage = signal('');
  errorMessage = signal('');

  get email() {
    return this.form.get('email')!;
  }

  get password() {
    return this.form.get('password')!;
  }

  togglePassword(): void {
    this.showPassword.update(v => !v);
  }

  async onLogin(): Promise<void> {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.loading.set(true);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const {email, password} = this.form.value as { email: string; password: string };
      const res = await this.auth.login({email, password});
      this.form.reset();
      this.successMessage.set(`Welcome back, ${res.user.username}!`);
      setTimeout(() => this.successMessage.set(''), 2500);
    } catch (error) {
      console.error(error);
      this.errorMessage.set('Invalid email or password.');
      setTimeout(() => this.errorMessage.set(''), 2500);
    } finally {
      this.loading.set(false);
    }
  }
}
