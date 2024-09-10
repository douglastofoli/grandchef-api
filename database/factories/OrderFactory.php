<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => 'open',
            'total_price' => 20.0,
            'created_at' => '2024-09-10T19:17:47.000000Z',
            'updated_at' => '2024-09-10T19:17:47.000000Z'
        ];
    }
}
