<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\PessoaRepositoryInterface;
use App\Repositories\DAOs\PessoaDAO;
use App\Repositories\Interfaces\FilmeRepositoryInterface;
use App\Repositories\DAOs\FilmeDAO;
use App\Repositories\Interfaces\LocacaoRepositoryInterface;
use App\Repositories\DAOs\LocacaoDAO;
use App\Models\Pessoa;
use App\Models\Filme;
use App\Models\Locacao;
use App\Observers\PessoaObserver;
use App\Observers\FilmeObserver;
use App\Observers\LocacaoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repositories
        $this->app->bind(PessoaRepositoryInterface::class, PessoaDAO::class);
        $this->app->bind(FilmeRepositoryInterface::class, FilmeDAO::class);
        $this->app->bind(LocacaoRepositoryInterface::class, LocacaoDAO::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Pessoa::observe(PessoaObserver::class);
        Filme::observe(FilmeObserver::class);
        Locacao::observe(LocacaoObserver::class);
    }
}
