import { Injectable, signal } from '@angular/core';
import { Message } from '../entity/message.entity';

@Injectable({ providedIn: 'root' })
export class MessageStreamService {
  private readonly connections = new Map<string, EventSource>();

  subscribeToMessages(userId: string) {
    console.log('Opening Mercure EventSource for user:', userId);
    const messages = signal<Message[]>([]);

    const url = `http://localhost:3000/.well-known/mercure?topic=/messages/${userId}`;
    const source = new EventSource(url);
    this.connections.set(userId, source);

    source.onopen = () => {
      console.log('Connected to Mercure stream');
    };

    source.onmessage = (event) => {
      console.log('Mercure message received:', event.data);
      try {
        const data = JSON.parse(event.data);
        messages.update((prev) => [...prev, data]);
      } catch (err) {
        console.error('Error parsing Mercure event', err);
      }
    };

    source.onerror = (error) => {
      console.error('Mercure connection error', error);
    };

    return messages.asReadonly();
  }

  disconnect(userId: string) {
    const src = this.connections.get(userId);
    if (src) {
      console.log('Closing Mercure connection for user:', userId);
      src.close();
      this.connections.delete(userId);
    }
  }
}
