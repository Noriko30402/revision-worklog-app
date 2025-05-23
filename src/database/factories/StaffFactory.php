<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Staff;


class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Staff::class;

    public function definition()
    {
        return [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
            ];
        }

        /**
         * Indicate that the model's email address should be unverified.
         *
         * @return \Illuminate\Database\Eloquent\Factories\Factory
         */
        public function unverified()
        {
            return $this->state(function (array $attributes) {
                return [
                    'email_verified_at' => null,
                ];
            });
        }
    }
