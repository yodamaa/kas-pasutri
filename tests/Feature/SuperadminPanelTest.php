<?php

namespace Tests\Feature;

use App\Models\Couple;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeCouple(): Couple
    {
        return Couple::create([
            'nama' => 'Test Couple',
            'kode' => 'TC001',
            'is_active' => true,
        ]);
    }

    private function makeUser(string $role, ?Couple $couple): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'@test.test',
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => true,
            'couple_id' => $couple?->id,
        ]);
    }

    public function test_superadmin_can_access_superadmin_dashboard(): void
    {
        $admin = $this->makeUser('superadmin', null);

        $this->actingAs($admin);

        $this->get('/superadmin')->assertOk();
    }

    public function test_regular_user_cannot_access_superadmin_panel(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('suami', $couple);

        $this->actingAs($user);

        $this->get('/superadmin')->assertForbidden();
    }

    public function test_superadmin_can_access_tenant_dashboard(): void
    {
        $couple = $this->makeCouple();
        $admin = $this->makeUser('superadmin', null);

        $this->actingAs($admin);

        $this->get('/admin/'.$couple->id)->assertOk();
    }

    public function test_regular_user_can_access_own_tenant_dashboard(): void
    {
        $couple = $this->makeCouple();
        $user = $this->makeUser('istri', $couple);

        $this->actingAs($user);

        $this->get('/admin/'.$couple->id)->assertOk();
    }

    public function test_guest_is_redirected_to_superadmin_login(): void
    {
        $this->get('/superadmin')->assertRedirect('/superadmin/login');
    }
}
