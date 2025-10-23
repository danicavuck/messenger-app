import {
  effect,
  inject,
  Injectable,
  Injector,
  runInInjectionContext,
  signal,
} from '@angular/core';
import { ApiService } from '../../core/services/api.service';
import { AuthService } from '../../auth/services/auth.service';
import { MessageStreamService } from './message-stream.service';
import { Message } from '../entity/message.entity';

interface MessageResponse {
  data: Message[];
}

@Injectable({ providedIn: 'root' })
export class MessageService {
  private readonly api = inject(ApiService);
  private readonly auth = inject(AuthService);
  private readonly stream = inject(MessageStreamService);
  private readonly injector = inject(Injector);

  private readonly _messages = signal<Message[]>([]);
  private readonly _loading = signal(false);
  private readonly _error = signal<string | null>(null);

  readonly messages = this._messages.asReadonly();
  readonly loading = this._loading.asReadonly();
  readonly error = this._error.asReadonly();

  /**
   * Loads all messages for the logged-in user
   * and subscribes to Mercure updates.
   */
  async loadAll(): Promise<void> {
    this._loading.set(true);
    this._error.set(null);

    try {
      const { data } = await this.api.get<MessageResponse>('/messages');
      const messages = data.map((m) => ({
        ...m,
        created_at: new Date(m.created_at),
      }));

      this._messages.set(messages);
      this.initializeStream();
    } catch (err) {
      console.error('[MessageService] loadAll error:', err);
      this._error.set('Failed to load messages.');
      this._messages.set([]);
    } finally {
      this._loading.set(false);
    }
  }

  /**
   * Sends a new message and applies optimistic UI updates.
   */
  async sendMessage(content: string): Promise<void> {
    const user = this.auth.loggedInUser$();
    if (!user) {
      this._error.set('You must be logged in to send messages.');
      console.warn('[MessageService] Attempted to send message while logged out.');
      return;
    }

    const tempMessage: Message = {
      id: crypto.randomUUID(),
      content,
      user,
      status: 'pending',
      created_at: new Date(),
    };

    this._messages.update((prev) => [...prev, tempMessage]);

    try {
      const saved = await this.api.post<Message>('/messages', { content });
      this.replaceMessage(tempMessage.id!, {
        ...saved,
        created_at: new Date(saved.created_at),
        status: 'sent',
      });
    } catch (err) {
      console.error('[MessageService] sendMessage error:', err);
      this._error.set('Failed to send message.');
      this.replaceMessage(tempMessage.id!, { ...tempMessage, status: 'failed' });
    }
  }

  /**
   * Initializes the Mercure stream and applies live updates.
   */
  private initializeStream(): void {
    const user = this.auth.loggedInUser$();
    if (!user) return;

    const streamSignal = this.stream.subscribeToMessages(user.id);

    runInInjectionContext(this.injector, () => {
      effect(
        () => {
          const incoming = streamSignal();
          if (!incoming.length) return;

          this._messages.update((current) => this.mergeIncoming(current, incoming));
        },
        { allowSignalWrites: true }
      );
    });
  }

  /**
   * Merges new messages from the stream with the current state.
   */
  private mergeIncoming(current: Message[], incoming: Message[]): Message[] {
    const existingIds = new Set(current.map((m) => m.id));
    const updated = [...current];

    for (const msg of incoming) {
      // Replace optimistic pending messages
      const optimistic = updated.find(
        (m) =>
          m.status === 'pending' &&
          m.content === msg.content &&
          m.user.id === msg.user.id
      );

      if (optimistic) {
        Object.assign(optimistic, msg);
        continue;
      }

      if (!existingIds.has(msg.id)) {
        updated.push(msg);
      }
    }

    // Sort chronologically (user messages before bot if same timestamp)
    return updated.sort((a, b) => {
      const diff =
        new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
      if (diff !== 0) return diff;
      if (a.isBot === b.isBot) return 0;
      return a.isBot ? 1 : -1; // user message first
    });
  }

  /**
   * Replaces a message in-place by ID.
   */
  private replaceMessage(id: string, updated: Message): void {
    this._messages.update((messages) =>
      messages.map((m) => (m.id === id ? updated : m))
    );
  }


  /** Disconnects Mercure and resets state. */
  disconnect(): void {
    const user = this.auth.loggedInUser$();
    if (user) this.stream.disconnect(user.id);
    this._messages.set([]);
  }
}
