import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';
import { EmployeeFormComponent } from './components/employee-form.component';

@NgModule({
  declarations: [EmployeeFormComponent],
  imports: [CommonModule, ReactiveFormsModule, SharedModule],
  exports: [EmployeeFormComponent],
})
export class EmployeeModule {}
