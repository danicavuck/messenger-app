import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { NgClass} from '@angular/common';
import { MessageService } from '../../services/message.service';
import { MessageComponent } from '../message/message.component';
import { AuthService } from '../../../auth/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-chat',
  standalone: true,
  imports: [NgClass, MessageComponent],
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.css'],
})
export class ChatComponent implements OnInit {
  private readonly messageService = inject(MessageService);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  protected _messages$ = this.messageService.messages;
  protected _loading$ = this.messageService.loading;
  protected _newMessage$ = signal('');

  protected _hasMessages$ = computed(() => this._messages$().length > 0);

  async ngOnInit(): Promise<void> {
    await this.messageService.loadAll();
  }

  trackByIndex(index: number): number {
    return index;
  }

  onInputChange(event: Event): void {
    const target = event.target as HTMLInputElement;
    this._newMessage$.set(target.value);
  }

  async onSend(): Promise<void> {
    const content = this._newMessage$().trim();
    if (!content) return;

    try {
      await this.messageService.sendMessage(content);
      this._newMessage$.set('');
    } catch (err) {
      console.error('Failed to send message', err);
    }
  }

  async logout() {
    // stop listening to Mercure
    this.messageService.disconnect();
    this.authService.logout();
    await this.router.navigate(['/login']);
  }
}
