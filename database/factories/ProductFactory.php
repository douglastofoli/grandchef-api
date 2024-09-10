<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Refrigerante',
            'price' => 5.5,
            'category_id' => Category::factory()->create()['id'],
            'created_at' => '2024-09-10T19:17:47.000000Z',
            'updated_at' => '2024-09-10T19:17:47.000000Z'
        ];
    }
}
