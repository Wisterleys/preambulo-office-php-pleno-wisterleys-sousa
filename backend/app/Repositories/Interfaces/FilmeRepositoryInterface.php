<?php

namespace App\Repositories\Interfaces;

use App\Models\Filme;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface FilmeRepositoryInterface
{
    public function all(): LengthAwarePaginator;
    
    public function findByUuid(string $uuid): ?Filme;
    
    public function create(array $data): Filme;
    
    public function update(string $uuid, array $data): Filme;
    
    public function delete(string $uuid): bool;
    
    public function findByCategoria(string $categoria): LengthAwarePaginator;
    
    public function findDisponiveis(): LengthAwarePaginator;
    
    public function getCatalogo(): Collection;
}
