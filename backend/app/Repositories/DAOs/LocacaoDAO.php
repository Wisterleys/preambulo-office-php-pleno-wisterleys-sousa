<?php

namespace App\Repositories\DAOs;

use App\Models\Locacao;
use App\Models\Pessoa;
use App\Repositories\Interfaces\LocacaoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LocacaoDAO implements LocacaoRepositoryInterface
{
    public function __construct(
        protected Locacao $model
    ) {}

    public function all(): LengthAwarePaginator
    {
        return $this->model
            ->with('pessoa', 'filmes')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function findByUuid(string $uuid): ?Locacao
    {
        return $this->model
            ->with('pessoa', 'filmes')
            ->where('uuid', $uuid)
            ->first();
    }

    public function create(array $data): Locacao
    {
        return $this->model->create($data);
    }

    public function update(string $uuid, array $data): Locacao
    {
        $locacao = $this->findByUuid($uuid);
        
        if (!$locacao) {
            throw new \Exception('Locação não encontrada');
        }

        $locacao->update($data);
        return $locacao->fresh();
    }

    public function delete(string $uuid): bool
    {
        $locacao = $this->findByUuid($uuid);
        
        if (!$locacao) {
            throw new \Exception('Locação não encontrada');
        }

        return $locacao->delete();
    }

    public function findByPessoa(string $pessoaUuid): LengthAwarePaginator
    {
        $pessoa = Pessoa::where('uuid', $pessoaUuid)->firstOrFail();
        
        return $this->model
            ->with('filmes')
            ->where('pessoa_id', $pessoa->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function findAtivas(): Collection
    {
        return $this->model
            ->with('pessoa', 'filmes')
            ->where('status', 'ativa')
            ->orderBy('data_prevista_devolucao')
            ->get();
    }

    public function findAtrasadas(): Collection
    {
        return $this->model
            ->with('pessoa', 'filmes')
            ->where('status', 'atrasada')
            ->orderBy('data_prevista_devolucao')
            ->get();
    }

    public function devolver(string $uuid): Locacao
    {
        $locacao = $this->findByUuid($uuid);
        
        if (!$locacao) {
            throw new \Exception('Locação não encontrada');
        }

        if ($locacao->status === 'devolvida') {
            throw new \Exception('Locação já foi devolvida');
        }

        $locacao->devolver();
        return $locacao->fresh();
    }
}
