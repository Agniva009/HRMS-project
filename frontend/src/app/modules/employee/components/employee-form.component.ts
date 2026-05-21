import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-employee-form',
  templateUrl: './employee-form.component.html',
})
export class EmployeeFormComponent implements OnInit {
  employeeForm!: FormGroup;

  constructor(private fb: FormBuilder) {}

  ngOnInit(): void {
    this.employeeForm = this.fb.group({
      employeeName: ['', Validators.required],
      fatherName: [''],
      motherName: [''],
      department: ['', Validators.required],
      designation: ['', Validators.required],
      address: [''],
      city: [''],
      state: [''],
      country: [''],

      // Exception fields — no appUppercase directive in the template
      email: ['', [Validators.required, Validators.email]],
      username: [''],
      password: ['', Validators.required],
    });
  }

  onSubmit(): void {
    if (this.employeeForm.valid) {
      console.log('Submitting employee:', this.employeeForm.value);
    }
  }
}
