<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_role' => Role::query()->value('id_role') ?? 1,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => true,
            'foto_profil' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Akun yang dinonaktifkan.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
