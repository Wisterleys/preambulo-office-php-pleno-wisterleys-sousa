<?php

namespace App\Repositories\Interfaces;

use App\Models\Locacao;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LocacaoRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    
    public function findByUuid(string $uuid): ?Locacao;
    
    public function create(array $data): Locacao;
    
    public function update(string $uuid, array $data): Locacao;
    
    public function delete(string $uuid): bool;
    
    public function findByPessoa(string $pessoaUuid): LengthAwarePaginator;
    
    public function findAtivas(): Collection;
    
    public function findAtrasadas(): Collection;
    
    public function devolver(string $uuid): Locacao;
}
