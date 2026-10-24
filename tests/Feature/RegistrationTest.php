<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registering_creates_a_school_and_an_admin_user_together()
    {
        $response = $this->post('/register', [
            'name' => 'Ryan Banda',
            'phone' => '+265999000000',
            'email' => 'ryan@school.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'school_name' => 'Kamuzu Academy',
            'institution_type' => 'secondary',
            'country' => 'Malawi',
            'terms' => '1',
        ]);

        $this->assertDatabaseHas('schools', ['name' => 'Kamuzu Academy', 'setup_completed' => false]);
        $this->assertDatabaseHas('users', ['email' => 'ryan@school.test', 'role' => 'school_admin']);

        $user = User::where('email', 'ryan@school.test')->first();
        $school = School::where('name', 'Kamuzu Academy')->first();

        $this->assertEquals($school->id, $user->school_id);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('setup.step1'));
    }

    /** @test */
    public function registration_fails_without_agreeing_to_terms()
    {
        $response = $this->post('/register', [
            'name' => 'Ryan Banda',
            'phone' => '+265999000000',
            'email' => 'ryan@school.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'school_name' => 'Kamuzu Academy',
            'institution_type' => 'secondary',
            'country' => 'Malawi',
            // 'terms' omitted
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertDatabaseMissing('schools', ['name' => 'Kamuzu Academy']);
    }

    /** @test */
    public function duplicate_email_is_rejected()
    {
        User::factory()->create(['email' => 'taken@school.test']);

        $response = $this->post('/register', [
            'name' => 'Someone',
            'phone' => '+265999000001',
            'email' => 'taken@school.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'school_name' => 'Another School',
            'institution_type' => 'primary',
            'country' => 'Malawi',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('email');
    }
}