<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
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
    public function definition()
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'sku' => fake()->unique()->bothify('SWB-###-????'),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 15000, 350000),
            'stock' => fake()->numberBetween(5, 50),
            'minimum_stock' => 10,
            'unit' => fake()->randomElement(['pcs', 'slice', 'loaf', 'box']),
            'description' => fake()->sentence(),
        ];
    }
}
