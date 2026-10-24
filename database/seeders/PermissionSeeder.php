<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Results' => [
                'results.view' => 'View Results',
                'results.enter' => 'Enter Results',
                'results.edit' => 'Edit Results',
                'results.approve' => 'Approve Results',
                'results.publish' => 'Publish Results',
            ],
            'Assessments' => [
                'assessment.create' => 'Create Assessment',
                'assessment.edit' => 'Edit Assessment',
                'assessment.moderate' => 'Moderate Assessment',
            ],
            'Students' => [
                'students.view' => 'View Students',
                'students.edit' => 'Edit Students',
            ],
            'Reports & Settings' => [
                'reports.generate' => 'Generate Reports',
                'reports.export' => 'Export Reports',
                'settings.assessment' => 'Manage Assessment Settings',
                'settings.grading' => 'Manage Grading Settings',
                'settings.promotion' => 'Manage Promotion Settings',
            ],
        ];

        foreach ($permissions as $group => $keys) {
            foreach ($keys as $key => $label) {
                Permission::updateOrCreate(
                    ['key' => $key],
                    ['label' => $label, 'group' => $group]
                );
            }
        }
    }
}