<?php

namespace App\Repositories\DAOs;

use App\Models\Filme;
use App\Repositories\Interfaces\FilmeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FilmeDAO implements FilmeRepositoryInterface
{
    public function __construct(
        protected Filme $model
    ) {}

    public function all(): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('titulo')
            ->paginate(20);
    }

    public function findByUuid(string $uuid): ?Filme
    {
        return $this->model
            ->where('uuid', $uuid)
            ->first();
    }

    public function create(array $data): Filme
    {
        return $this->model->create($data);
    }

    public function update(string $uuid, array $data): Filme
    {
        $filme = $this->findByUuid($uuid);
        
        if (!$filme) {
            throw new \Exception('Filme não encontrado');
        }

        $filme->update($data);
        return $filme->fresh();
    }

    public function delete(string $uuid): bool
    {
        $filme = $this->findByUuid($uuid);
        
        if (!$filme) {
            throw new \Exception('Filme não encontrado');
        }

        return $filme->delete();
    }

    public function findByCategoria(string $categoria): LengthAwarePaginator
    {
        return $this->model
            ->where('categoria', $categoria)
            ->orderBy('titulo')
            ->paginate(20);
    }

    public function findDisponiveis(): LengthAwarePaginator
    {
        return $this->model
            ->where('quantidade_disponivel', '>', 0)
            ->orderBy('titulo')
            ->paginate(20);
    }

    public function getCatalogo(): Collection
    {
        return Cache::tags(['filmes', 'catalogo'])->remember('catalogo_completo', 3600, function () {
            return $this->model
                ->select('uuid', 'titulo', 'sinopse', 'ano', 'categoria', 'valor_locacao', 'quantidade_disponivel', 'imagem_path')
                ->orderBy('titulo')
                ->get();
        });
    }
}
