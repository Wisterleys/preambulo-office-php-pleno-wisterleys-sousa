<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pessoa>
 */
class PessoaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // cria usuário se não existir
            'nome_completo' => $this->faker->name(),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'telefone' => $this->faker->phoneNumber(),
            'data_nascimento' => $this->faker->date('Y-m-d', '2005-01-01'),
            'role' => $this->faker->randomElement(['admin', 'attendant', 'customer']),
            'endereco' => $this->faker->address(),
        ];
    }
}
