<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    private array $roles = [
        'Super Admin' => 'super_admin',
        'School Admin' => 'school_admin',
        'Academic Director' => 'academic_director',
        'Head of Dept' => 'head_of_department',
        'Teacher' => 'teacher',
        'Student' => 'student',
        'Parent' => 'parent',
    ];

    public function edit(Request $request)
    {
        $schoolId = $request->user()->school_id;

        $permissionGroups = Permission::all()->groupBy('group');

        $grants = DB::table('role_permission')
            ->join('permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->where('role_permission.school_id', $schoolId)
            ->select('role_permission.role', 'permissions.key')
            ->get()
            ->map(fn($row) => "{$row->role}|{$row->key}")
            ->flip();

        return view('settings.roles', [
            'permissionGroups' => $permissionGroups,
            'roles' => $this->roles,
            'grants' => $grants,
        ]);
    }

    public function update(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $data = $request->validate(['permissions' => 'array']);

        DB::transaction(function () use ($data, $schoolId) {
            DB::table('role_permission')->where('school_id', $schoolId)->delete();

            foreach ($data['permissions'] ?? [] as $roleSlug => $permissionKeys) {
                $role = str_replace('-', '_', $roleSlug);

                foreach ($permissionKeys as $key) {
                    $permission = Permission::where('key', $key)->first();
                    if (!$permission) {
                        continue;
                    }

                    DB::table('role_permission')->insert([
                        'school_id' => $schoolId,
                        'role' => $role,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Permissions updated.');
    }
}