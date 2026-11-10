<?php

namespace Database\Seeders;

use App\Models\RegistrationField;
use Illuminate\Database\Seeder;

class RegistrationFieldSeeder extends Seeder
{
    /**
     * Seed the master registration field library.
     */
    public function run(): void
    {
        $fields = [
            ['field_key' => 'full_name', 'label' => 'Name', 'field_type' => 'text', 'sort_order' => 1],
            ['field_key' => 'father_name', 'label' => 'Father Name', 'field_type' => 'text', 'sort_order' => 2],
            ['field_key' => 'date_of_birth', 'label' => 'Date of Birth', 'field_type' => 'date', 'sort_order' => 3],
            [
                'field_key' => 'gender',
                'label' => 'Gender',
                'field_type' => 'select',
                'options' => ['Male', 'Female', 'Other'],
                'sort_order' => 4,
            ],
            ['field_key' => 'mobile', 'label' => 'Mobile / WhatsApp No.', 'field_type' => 'tel', 'sort_order' => 5],
            ['field_key' => 'cnic', 'label' => 'CNIC', 'field_type' => 'text', 'validation_rules' => ['regex:/^\d{5}-\d{7}-\d$/'], 'sort_order' => 6],
            ['field_key' => 'registration_number', 'label' => 'Registration Number', 'field_type' => 'text', 'sort_order' => 7],
            ['field_key' => 'faculty', 'label' => 'Faculty', 'field_type' => 'select', 'sort_order' => 8],
            ['field_key' => 'department', 'label' => 'Department', 'field_type' => 'select', 'sort_order' => 9],
            ['field_key' => 'degree', 'label' => 'Degree', 'field_type' => 'text', 'sort_order' => 10],
            ['field_key' => 'designation', 'label' => 'Designation', 'field_type' => 'text', 'sort_order' => 11],
            ['field_key' => 'organization', 'label' => 'Organization', 'field_type' => 'text', 'sort_order' => 12],
            ['field_key' => 'email', 'label' => 'Email', 'field_type' => 'email', 'sort_order' => 13],
            ['field_key' => 'abstract', 'label' => 'Abstract', 'field_type' => 'textarea', 'sort_order' => 14],
            ['field_key' => 'attachment', 'label' => 'File Upload', 'field_type' => 'file', 'sort_order' => 15],
        ];

        foreach ($fields as $field) {
            RegistrationField::updateOrCreate(
                ['field_key' => $field['field_key']],
                [
                    'label' => $field['label'],
                    'field_type' => $field['field_type'],
                    'options' => $field['options'] ?? null,
                    'validation_rules' => $field['validation_rules'] ?? null,
                    'is_active' => true,
                    'sort_order' => $field['sort_order'],
                ]
            );
        }
    }
}
