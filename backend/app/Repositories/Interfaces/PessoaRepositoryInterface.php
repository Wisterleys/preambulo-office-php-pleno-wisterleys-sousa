<?php

namespace App\Repositories\Interfaces;

use App\Models\Pessoa;
use Illuminate\Pagination\LengthAwarePaginator;

interface PessoaRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    
    public function findByUuid(string $uuid): ?Pessoa;
    
    public function findByUserId(int $userId): ?Pessoa;
    
    public function findByCpf(string $cpf): ?Pessoa;
    
    public function create(array $data): Pessoa;
    
    public function update(string $uuid, array $data): Pessoa;
    
    public function delete(string $uuid): bool;
    
    public function findByRole(string $role): LengthAwarePaginator;
}
