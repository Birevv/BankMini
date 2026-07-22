<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password123'),
            'nama_petugas' => fake()->name(),
            'role' => UserRole::TELLER,
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (): array => ['role' => UserRole::ADMIN]);
    }

    public function teller(): static
    {
        return $this->state(fn (): array => ['role' => UserRole::TELLER]);
    }

    public function supervisor(): static
    {
        return $this->state(fn (): array => ['role' => UserRole::SUPERVISOR]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
