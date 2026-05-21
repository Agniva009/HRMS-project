import { Component } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { FormsModule, ReactiveFormsModule, FormControl, FormGroup } from '@angular/forms';
import { By } from '@angular/platform-browser';

import { UppercaseDirective } from './uppercase.directive';

/* ---------- Test host: template-driven ---------- */
@Component({
  template: `<input appUppercase [(ngModel)]="name" />`,
})
class TemplateDrivenHost {
  name = '';
}

/* ---------- Test host: reactive ---------- */
@Component({
  template: `
    <form [formGroup]="form">
      <input appUppercase formControlName="city" />
    </form>
  `,
})
class ReactiveHost {
  form = new FormGroup({ city: new FormControl('') });
}

/* ---------- Test host: plain element ---------- */
@Component({
  template: `<input appUppercase id="plain" />`,
})
class PlainHost {}

/* ---------- Test host: exception field (no directive) ---------- */
@Component({
  template: `<input id="email" [(ngModel)]="email" />`,
})
class ExceptionHost {
  email = '';
}

describe('UppercaseDirective', () => {
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormsModule, ReactiveFormsModule],
      declarations: [
        UppercaseDirective,
        TemplateDrivenHost,
        ReactiveHost,
        PlainHost,
        ExceptionHost,
      ],
    }).compileComponents();
  });

  function typeInto(
    fixture: ComponentFixture<unknown>,
    selector: string,
    value: string
  ): HTMLInputElement {
    const input: HTMLInputElement = fixture.debugElement.query(
      By.css(selector)
    ).nativeElement;
    input.value = value;
    input.dispatchEvent(new Event('input'));
    fixture.detectChanges();
    return input;
  }

  it('should convert template-driven input to uppercase', async () => {
    const fixture = TestBed.createComponent(TemplateDrivenHost);
    fixture.detectChanges();
    await fixture.whenStable();

    const input = typeInto(fixture, 'input', 'hello world');
    expect(input.value).toBe('HELLO WORLD');
  });

  it('should convert reactive form input to uppercase', () => {
    const fixture = TestBed.createComponent(ReactiveHost);
    fixture.detectChanges();

    typeInto(fixture, 'input', 'new york');
    expect(fixture.componentInstance.form.get('city')?.value).toBe('NEW YORK');
  });

  it('should convert plain element input to uppercase', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input = typeInto(fixture, '#plain', 'mixed Case');
    expect(input.value).toBe('MIXED CASE');
  });

  it('should preserve cursor position', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input: HTMLInputElement = fixture.debugElement.query(
      By.css('#plain')
    ).nativeElement;
    input.value = 'abcde';
    input.setSelectionRange(3, 3);
    input.dispatchEvent(new Event('input'));
    fixture.detectChanges();

    expect(input.selectionStart).toBe(3);
    expect(input.selectionEnd).toBe(3);
  });

  it('should NOT touch fields without the directive (exception fields)', async () => {
    const fixture = TestBed.createComponent(ExceptionHost);
    fixture.detectChanges();
    await fixture.whenStable();

    const input = typeInto(fixture, '#email', 'User@Example.com');
    expect(input.value).toBe('User@Example.com');
  });

  it('should handle already-uppercase input without looping', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input = typeInto(fixture, '#plain', 'ALREADY');
    expect(input.value).toBe('ALREADY');
  });

  it('should handle empty input gracefully', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input = typeInto(fixture, '#plain', '');
    expect(input.value).toBe('');
  });

  it('should handle numeric and special character input', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input = typeInto(fixture, '#plain', 'apt 12-b, floor #3');
    expect(input.value).toBe('APT 12-B, FLOOR #3');
  });

  it('should NOT convert during IME composition', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input: HTMLInputElement = fixture.debugElement.query(
      By.css('#plain')
    ).nativeElement;

    input.dispatchEvent(new Event('compositionstart'));

    input.value = 'にほん';
    input.dispatchEvent(new Event('input'));
    fixture.detectChanges();

    expect(input.value).toBe('にほん');
  });

  it('should convert after IME compositionend', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input: HTMLInputElement = fixture.debugElement.query(
      By.css('#plain')
    ).nativeElement;

    input.dispatchEvent(new Event('compositionstart'));

    input.value = 'abc';
    input.dispatchEvent(new Event('input'));
    fixture.detectChanges();
    expect(input.value).toBe('abc');

    input.dispatchEvent(new Event('compositionend'));
    fixture.detectChanges();
    expect(input.value).toBe('ABC');
  });

  it('should resume normal uppercasing after composition ends', () => {
    const fixture = TestBed.createComponent(PlainHost);
    fixture.detectChanges();

    const input: HTMLInputElement = fixture.debugElement.query(
      By.css('#plain')
    ).nativeElement;

    input.dispatchEvent(new Event('compositionstart'));
    input.value = 'test';
    input.dispatchEvent(new Event('input'));
    expect(input.value).toBe('test');

    input.dispatchEvent(new Event('compositionend'));
    expect(input.value).toBe('TEST');

    input.value = 'hello';
    input.dispatchEvent(new Event('input'));
    fixture.detectChanges();
    expect(input.value).toBe('HELLO');
  });
});
