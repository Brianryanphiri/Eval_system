<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class DefaultRolePermissionsService
{
    /**
     * The starting permission set for each role — matches the $defaultGrants
     * array used as placeholder data on the Roles & Permissions page, so what
     * the admin sees pre-checked on first visit matches what's actually stored.
     */
    private array $defaults = [
        'results.view' => ['super_admin', 'school_admin', 'academic_director', 'head_of_department', 'teacher', 'student', 'parent'],
        'results.enter' => ['super_admin', 'school_admin', 'teacher'],
        'results.edit' => ['super_admin', 'school_admin', 'academic_director'],
        'results.approve' => ['super_admin', 'academic_director'],
        'results.publish' => ['super_admin', 'academic_director'],
        'assessment.create' => ['super_admin', 'school_admin', 'teacher'],
        'assessment.edit' => ['super_admin', 'school_admin', 'teacher'],
        'assessment.moderate' => ['super_admin', 'academic_director', 'head_of_department'],
        'students.view' => ['super_admin', 'school_admin', 'academic_director', 'head_of_department', 'teacher', 'parent'],
        'students.edit' => ['super_admin', 'school_admin'],
        'reports.generate' => ['super_admin', 'school_admin', 'academic_director', 'head_of_department', 'teacher'],
        'reports.export' => ['super_admin', 'school_admin', 'academic_director'],
        'settings.assessment' => ['super_admin', 'school_admin'],
        'settings.grading' => ['super_admin', 'school_admin'],
        'settings.promotion' => ['super_admin', 'school_admin'],
    ];

    public function seedForSchool(School $school): void
    {
        // Idempotent — safe to call again without duplicating rows
        DB::table('role_permission')->where('school_id', $school->id)->delete();

        $permissionIds = Permission::pluck('id', 'key');

        $rows = [];
        foreach ($this->defaults as $key => $roles) {
            $permissionId = $permissionIds->get($key);
            if (!$permissionId)
                continue;

            foreach ($roles as $role) {
                $rows[] = [
                    'school_id' => $school->id,
                    'role' => $role,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('role_permission')->insert($rows);
    }
}