<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupWizardMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_whose_school_has_not_completed_setup_is_redirected_into_the_wizard()
    {
        $school = School::create([
            'name' => 'New School',
            'institution_type' => 'secondary',
            'country' => 'Malawi',
            'setup_completed' => false,
            'setup_step' => 3,
        ]);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin',
            'email' => 'a@a.test',
            'role' => 'school_admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Middleware should bounce them to whichever step they left off on
        $response->assertRedirect(route('setup.step3'));
    }

    /** @test */
    public function a_user_whose_school_has_completed_setup_can_reach_the_dashboard()
    {
        $school = School::create([
            'name' => 'Ready School',
            'institution_type' => 'secondary',
            'country' => 'Malawi',
            'setup_completed' => true,
            'setup_step' => 5,
        ]);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin',
            'email' => 'b@b.test',
            'role' => 'school_admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }
}