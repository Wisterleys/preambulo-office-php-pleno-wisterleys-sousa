<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filme extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'filmes';

    protected $fillable = [
        'titulo',
        'sinopse',
        'ano',
        'categoria',
        'valor_locacao',
        'quantidade_disponivel',
        'imagem_path',
    ];

    protected $casts = [
        'valor_locacao' => 'decimal:2',
        'quantidade_disponivel' => 'integer',
        'ano' => 'integer',
    ];

    protected $hidden = [
        'id',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function locacoes(): BelongsToMany
    {
        return $this->belongsToMany(Locacao::class, 'locacao_filme')
            ->withPivot('quantidade', 'valor_unitario')
            ->withTimestamps();
    }

    public function temDisponibilidade(int $quantidade = 1): bool
    {
        return $this->quantidade_disponivel >= $quantidade;
    }

    public function reduzirQuantidade(int $quantidade = 1): void
    {
        $this->decrement('quantidade_disponivel', $quantidade);
    }

    public function aumentarQuantidade(int $quantidade = 1): void
    {
        $this->increment('quantidade_disponivel', $quantidade);
    }
}
