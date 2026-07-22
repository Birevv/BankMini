<?php

namespace Tests\Feature;

use App\Actions\Transactions\CreateDepositAction;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use LogicException;
use Tests\TestCase;

class SecurityInvariantTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordsAndPinsAreNeverStoredAsPlaintext(): void
    {
        $user = User::factory()->create(['password' => 'rahasia-petugas']);
        $customer = Nasabah::factory()->create([
            'pin_keamanan' => '654321',
            'portal_password' => 'rahasia-nasabah',
        ]);

        $this->assertNotSame('rahasia-petugas', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('rahasia-petugas', $user->getRawOriginal('password')));
        $this->assertNotSame('654321', $customer->getRawOriginal('pin_keamanan'));
        $this->assertTrue(Hash::check('654321', $customer->getRawOriginal('pin_keamanan')));
        $this->assertNotSame('rahasia-nasabah', $customer->getRawOriginal('portal_password'));
        $this->assertTrue(Hash::check('rahasia-nasabah', $customer->getRawOriginal('portal_password')));
    }

    public function testAccountNumberCannotChangeAfterFirstTransaction(): void
    {
        $teller = User::factory()->teller()->create();
        $customer = Nasabah::factory()->create();
        app(CreateDepositAction::class)->execute($teller, $customer->id, 50_000, true, true);

        $this->expectException(LogicException::class);
        $customer->update(['no_rekening' => 'BM-2099-999999']);
    }

    public function testWebResponsesIncludeDefensiveSecurityHeaders(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'same-origin')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    }

    public function testSessionUsesEncryptedHttpOnlySameSiteCookieConfiguration(): void
    {
        $this->assertTrue(config('session.encrypt'));
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
    }
}
