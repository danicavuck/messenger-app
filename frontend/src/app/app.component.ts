import {Component} from '@angular/core';
import {RouterOutlet} from "@angular/router";

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet],
  template: `
    <main class="max-w-4xl mx-auto">
      <div class="flex items-center justify-center gap-3 mb-6 mt-5">
        <img
          src="../assets/logos/digistore_logo.svg"
          alt="Messenger Logo"
          class="h-10 w-auto"
        />
        <h1 class="text-2xl font-bold text-gray-800 pt-1">ChatBot</h1>
      </div>
      <router-outlet></router-outlet>
    </main>
  `,
})
export class AppComponent {
}
