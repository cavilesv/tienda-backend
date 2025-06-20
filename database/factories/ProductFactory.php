<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    static $i = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /* $myfile = fopen(public_path('imagenes_testing/hola.txt'), "w"); */
        $this->faker->unique($reset = true);
        return [
            'name' => $this->faker->unique()->word() . ProductFactory::$i++,
            'price' => $this->faker->randomFloat(2, 1, 1000), // Ej: 23.45
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence(10),
            'mime' => 'image/jpeg'
        ];
    }
}
