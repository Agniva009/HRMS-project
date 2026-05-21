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
 * IME compositions (CJK scripts) are respected — conversion is deferred
 * until compositionend so intermediate characters are not corrupted.
 */
@Directive({
  selector: '[appUppercase]',
})
export class UppercaseDirective {
  private composing = false;

  constructor(
    private el: ElementRef<HTMLInputElement | HTMLTextAreaElement>,
    @Optional() @Self() private ngControl: NgControl
  ) {}

  @HostListener('compositionstart')
  onCompositionStart(): void {
    this.composing = true;
  }

  @HostListener('compositionend')
  onCompositionEnd(): void {
    this.composing = false;
    this.toUpperCase();
  }

  @HostListener('input')
  onInput(): void {
    if (this.composing) {
      return;
    }
    this.toUpperCase();
  }

  @HostListener('paste')
  onPaste(): void {
    setTimeout(() => {
      if (!this.composing) {
        this.toUpperCase();
      }
    });
  }

  private toUpperCase(): void {
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
}
