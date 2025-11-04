<?php

namespace App\Repositories\DAOs;

use App\Models\Pessoa;
use App\Repositories\Interfaces\PessoaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PessoaDAO implements PessoaRepositoryInterface
{
    public function __construct(
        protected Pessoa $model
    ) {}

    public function all(): LengthAwarePaginator
    {
        return $this->model
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function findByUuid(string $uuid): ?Pessoa
    {
        return $this->model
            ->with('user', 'locacoes')
            ->where('uuid', $uuid)
            ->first();
    }

    public function findByUserId(int $userId): ?Pessoa
    {
        return $this->model
            ->with('user')
            ->where('user_id', $userId)
            ->first();
    }

    public function findByCpf(string $cpf): ?Pessoa
    {
        return $this->model
            ->where('cpf', $cpf)
            ->first();
    }

    public function create(array $data): Pessoa
    {
        return $this->model->create($data);
    }

    public function update(string $uuid, array $data): Pessoa
    {
        $pessoa = $this->findByUuid($uuid);
        
        if (!$pessoa) {
            throw new \Exception('Pessoa não encontrada');
        }

        $pessoa->update($data);
        return $pessoa->fresh();
    }

    public function delete(string $uuid): bool
    {
        $pessoa = $this->findByUuid($uuid);
        
        if (!$pessoa) {
            throw new \Exception('Pessoa não encontrada');
        }

        return $pessoa->delete();
    }

    public function findByRole(string $role): LengthAwarePaginator
    {
        return $this->model
            ->with('user')
            ->where('role', $role)
            ->orderBy('nome_completo')
            ->paginate(15);
    }
}
