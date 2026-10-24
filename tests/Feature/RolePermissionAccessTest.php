<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function super_admin_always_passes_permission_checks_even_with_no_role_permission_rows()
    {
        $school = School::create(['name' => 'S', 'institution_type' => 'secondary', 'country' => 'Malawi', 'setup_completed' => true, 'setup_step' => 5]);
        $superAdmin = User::create(['school_id' => $school->id, 'name' => 'Super', 'email' => 'super@t.test', 'role' => 'super_admin', 'password' => bcrypt('x')]);

        $this->assertTrue($superAdmin->can_('results.approve'));
        $this->assertTrue($superAdmin->can_('anything.at.all')); // even a nonsense key — super_admin short-circuits
    }

    /** @test */
    public function a_role_without_a_granted_permission_returns_false()
    {
        $school = School::create(['name' => 'S', 'institution_type' => 'secondary', 'country' => 'Malawi', 'setup_completed' => true, 'setup_step' => 5]);
        $teacher = User::create(['school_id' => $school->id, 'name' => 'T', 'email' => 't@t.test', 'role' => 'teacher', 'password' => bcrypt('x')]);

        // No role_permission rows seeded at all in this test
        $this->assertFalse($teacher->can_('results.approve'));
    }

    /** @test */
    public function permission_grants_are_scoped_per_school()
    {
        $schoolA = School::create(['name' => 'A', 'institution_type' => 'secondary', 'country' => 'Malawi', 'setup_completed' => true, 'setup_step' => 5]);
        $schoolB = School::create(['name' => 'B', 'institution_type' => 'secondary', 'country' => 'Malawi', 'setup_completed' => true, 'setup_step' => 5]);

        $permission = \App\Models\Permission::create(['key' => 'results.approve', 'label' => 'Approve Results', 'group' => 'Results']);

        \DB::table('role_permission')->insert([
            'school_id' => $schoolA->id,
            'role' => 'teacher',
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $teacherAtA = User::create(['school_id' => $schoolA->id, 'name' => 'TA', 'email' => 'ta@t.test', 'role' => 'teacher', 'password' => bcrypt('x')]);
        $teacherAtB = User::create(['school_id' => $schoolB->id, 'name' => 'TB', 'email' => 'tb@t.test', 'role' => 'teacher', 'password' => bcrypt('x')]);

        $this->assertTrue($teacherAtA->can_('results.approve'));
        $this->assertFalse($teacherAtB->can_('results.approve')); // same role, different school — not granted there
    }
}