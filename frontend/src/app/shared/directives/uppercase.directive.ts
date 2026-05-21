import { Directive, ElementRef, HostListener, Self, Optional } from '@angular/core';
import { NgControl } from '@angular/forms';

/**
 * Reusable directive that transforms user input to UPPERCASE in real-time.
 *
 * Usage:
 *   <input appUppercase formControlName="employeeName" />
 *   <input appUppercase [(ngModel)]="city" />
 *   <input appUppercase />          <!-- plain element without forms -->
 *
 * Cursor position is preserved so the UX feels seamless.
 */
@Directive({
  selector: '[appUppercase]',
})
export class UppercaseDirective {
  constructor(
    private el: ElementRef<HTMLInputElement | HTMLTextAreaElement>,
    @Optional() @Self() private ngControl: NgControl
  ) {}

  @HostListener('input', ['$event'])
  onInput(event: Event): void {
    const input = this.el.nativeElement;
    const start = input.selectionStart;
    const end = input.selectionEnd;

    const upper = input.value.toUpperCase();

    if (upper === input.value) {
      return;
    }

    if (this.ngControl?.control) {
      this.ngControl.control.setValue(upper, { emitEvent: true });
    } else {
      input.value = upper;
    }

    input.setSelectionRange(start, end);
  }

  @HostListener('paste', ['$event'])
  onPaste(): void {
    setTimeout(() => this.onInput(new Event('input')));
  }
}
