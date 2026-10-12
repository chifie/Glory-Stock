<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'price' => fake()->numberBetween(1000, 100000),
            'stock' => fake()->numberBetween(1, 50),
            'category_id' => Category::factory(),
        ];
    }
}
