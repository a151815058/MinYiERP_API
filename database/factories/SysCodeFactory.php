<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SysCodeFactory extends Factory
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
            'Uuid' => Str::uuid(),
            'Puuid' => '',
            'Paramcode' => $this->faker->unique()->word,
            'Param' => $this->faker->word,
            'Note' => '',
            'Createuser' => 'admin',
            'update_user' => 'admin',
            'CreateTime' => now(),
            'update_time' => now(),            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            
        ]);
    }
}
