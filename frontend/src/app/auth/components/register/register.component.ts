import {Component, inject, signal} from '@angular/core';
import {CommonModule} from '@angular/common';
import {ReactiveFormsModule, FormBuilder, Validators} from '@angular/forms';
import {AuthService} from '../../services/auth.service';
import {Router, RouterLink} from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent {
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  form = this.fb.group({
    email: ['', [Validators.required, Validators.pattern(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)]],
    username: ['', [Validators.required, Validators.minLength(3)]],
    password: ['', [Validators.required, Validators.minLength(8)]],
  });

  isSubmitting = signal(false);
  errorMessage = signal('');
  successMessage = signal('');
  showPassword = signal(false);

  get email() {
    return this.form.get('email')!;
  }

  get username() {
    return this.form.get('username')!;
  }

  get password() {
    return this.form.get('password')!;
  }

  togglePassword(): void {
    this.showPassword.update(v => !v);
  }

  async onRegister() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const {email, username, password} = this.form.value as {
        email: string;
        username: string;
        password: string;
      };

      await this.authService.register({email, username, password});
      this.successMessage.set('Registration successful! Redirecting to login...');
      this.form.reset();
      setTimeout(() => {
        this.successMessage.set('');
        this.router.navigateByUrl('/login');
      }, 1000);
    } catch (error) {
      console.error(error);
      this.errorMessage.set('Registration failed. Please try again.');
      setTimeout(() => this.errorMessage.set(''), 3000);
    } finally {
      this.isSubmitting.set(false);
    }
  }
}
