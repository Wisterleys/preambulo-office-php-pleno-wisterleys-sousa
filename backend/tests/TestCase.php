<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /*
    |--------------------------------------------------------------------------
    | Consultas de contexto
    |--------------------------------------------------------------------------
    | Métodos para facilitar o acesso a coleções completas
    | ou carregadas com seus relacionamentos.
    */

    /** Retorna todos os usuários */
    public function allUsers()
    {
        return \App\Models\User::all();
    }

    /** Retorna todas as pessoas */
    public function allPessoas()
    {
        return \App\Models\Pessoa::all();
    }

    /** Retorna todas as pessoas com seus usuários */
    public function allPessoasWithUsers()
    {
        return \App\Models\Pessoa::with('user')->get();
    }

    /** Retorna todas as locações */
    public function allLocacoes()
    {
        return \App\Models\Locacao::all();
    }

    /** Retorna todas as locações com pessoa e filmes */
    public function allLocacoesWithPessoasAndFilmes()
    {
        return \App\Models\Locacao::with(['pessoa', 'filmes'])->get();
    }

    /** Retorna todos os filmes */
    public function allFilmes()
    {
        return \App\Models\Filme::all();
    }

    /** Retorna todos os filmes com locações */
    public function allFilmesWithLocacoes()
    {
        return \App\Models\Filme::with('locacoes')->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Seeders
    |--------------------------------------------------------------------------
    | Métodos utilitários para popular o banco durante os testes.
    */

    protected function seedTestUsers(): void
    {
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    protected function seedTestPessoas(): void
    {
        $this->artisan('db:seed', ['--class' => 'PessoaSeeder']);
    }

    protected function seedTestFilmes(): void
    {
        $this->artisan('db:seed', ['--class' => 'FilmeSeeder']);
    }

    protected function seedTestLocacoes(): void
    {
        $this->artisan('db:seed', ['--class' => 'LocacaoSeeder']);
    }

    /*
    |--------------------------------------------------------------------------
    | Mock Factories
    |--------------------------------------------------------------------------
    | Métodos rápidos para criar mocks de entidades isoladas
    | ou compostas, usando model factories.
    */

    protected function mockFilmes(int $qtd = 5)
    {
        return \App\Models\Filme::factory()->count($qtd)->create();
    }

    protected function mockPessoas(int $qtd = 5)
    {
        return \App\Models\Pessoa::factory()->count($qtd)->create();
    }

    protected function mockLocacoesComFilmes(int $qtd = 3)
    {
        $filmes = \App\Models\Filme::factory()->count(10)->create();
        return \App\Models\Locacao::factory()
            ->count($qtd)
            ->hasAttached($filmes->random(3), ['quantidade' => 1, 'valor_unitario' => 10.00])
            ->create();
    }
}
