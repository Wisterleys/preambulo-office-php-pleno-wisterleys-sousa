<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pessoa;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locacao>
 */
class LocacaoFactory extends Factory
{
    public function definition(): array
    {
        $inicio = Carbon::now()->subDays(rand(1, 10));

        return [
            'pessoa_id' => Pessoa::factory(),
            'data_inicio' => $inicio,
            'data_prevista_devolucao' => $inicio->copy()->addDays(3),
            'data_devolucao' => null,
            'valor_total' => 0,
            'multa_total' => 0,
            'status' => 'ativa',
        ];
    }
}
