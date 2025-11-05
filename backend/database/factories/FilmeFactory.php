<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filme>
 */
class FilmeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->unique()->sentence(3),
            'sinopse' => $this->faker->text(150),
            'ano' => $this->faker->year(),
            'categoria' => $this->faker->randomElement(['Ação', 'Drama', 'Comédia', 'Ficção Científica', 'Terror']),
            'valor_locacao' => $this->faker->randomFloat(2, 5, 20),
            'quantidade_disponivel' => $this->faker->numberBetween(1, 10),
        ];
    }
}
