<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function testAdminCanAccessOnlyAdminPanel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/teller')->assertForbidden();
        $this->actingAs($admin)->get('/supervisor')->assertForbidden();
    }

    public function testTellerCanAccessOnlyTellerPanel(): void
    {
        $teller = User::factory()->teller()->create();

        $this->actingAs($teller)->get('/teller')->assertOk();
        $this->actingAs($teller)->get('/admin')->assertForbidden();
        $this->actingAs($teller)->get('/supervisor')->assertForbidden();
    }

    public function testSupervisorCanAccessOnlySupervisorPanel(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $this->actingAs($supervisor)->get('/supervisor')->assertOk();
        $this->actingAs($supervisor)->get('/admin')->assertForbidden();
        $this->actingAs($supervisor)->get('/teller')->assertForbidden();
    }

    public function testInactiveUserCannotAccessInternalPanel(): void
    {
        $inactiveTeller = User::factory()->teller()->inactive()->create();

        $this->actingAs($inactiveTeller)->get('/teller')->assertForbidden();
    }

    public function testCentralLoginRedirectsEachRoleToItsOwnPanel(): void
    {
        foreach (['admin', 'teller', 'supervisor'] as $role) {
            $user = User::factory()->{$role}()->create([
                'username' => $role,
                'password' => 'password123',
            ]);

            $this->post(route('internal.login.store'), [
                'username' => $user->username,
                'password' => 'password123',
            ])->assertRedirect(url($role));

            auth()->logout();
            $this->app['session']->flush();
        }
    }

    public function testPanelLoginUrlsRedirectToCentralLogin(): void
    {
        foreach (['admin', 'teller', 'supervisor'] as $panel) {
            $this->get("/{$panel}/login")->assertRedirect(route('internal.login'));
        }
    }

    public function testHomepageStaffButtonReturnsAuthenticatedUserToTheirPanel(): void
    {
        foreach (['admin', 'teller', 'supervisor'] as $role) {
            $user = User::factory()->{$role}()->create();

            $this->actingAs($user)
                ->get(route('internal.login'))
                ->assertRedirect(url($role));

            auth()->logout();
            $this->app['session']->flush();
        }
    }
}
