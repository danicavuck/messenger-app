import {Component, computed, inject, Input} from '@angular/core';
import {CommonModule, NgClass} from '@angular/common';
import {Message} from "../../entity/message.entity";
import {AuthService} from "../../../auth/services/auth.service";

@Component({
  selector: 'app-message',
  standalone: true,
  imports: [CommonModule, NgClass],
  templateUrl: './message.component.html',
  styleUrls: ['./message.component.css'],
})
export class MessageComponent {
  @Input({required: true}) message!: Message;
  @Input() index?: number;

  protected auth = inject(AuthService);

  get isPending(): boolean {
    return this.message.status === 'pending';
  }

  get isFailed(): boolean {
    return this.message.status === 'failed';
  }

  get isSent(): boolean {
    return this.message.status === 'sent';
  }
}
