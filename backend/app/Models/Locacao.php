<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Locacao extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'locacoes';

    protected $fillable = [
        'pessoa_id',
        'data_inicio',
        'data_prevista_devolucao',
        'data_devolucao',
        'status',
        'valor_total',
        'multa_total',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_prevista_devolucao' => 'date',
        'data_devolucao' => 'date',
        'valor_total' => 'decimal:2',
        'multa_total' => 'decimal:2',
    ];

    protected $hidden = [
        'id',
        'pessoa_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function filmes(): BelongsToMany
    {
        return $this->belongsToMany(Filme::class, 'locacao_filme')
            ->withPivot('quantidade', 'valor_unitario')
            ->withTimestamps();
    }

    public function estaAtrasada(): bool
    {
        if ($this->status === 'devolvida') {
            return false;
        }
        return Carbon::now()->isAfter($this->data_prevista_devolucao);
    }

    public function calcularMulta(): float
    {
        if (!$this->estaAtrasada()) {
            return 0;
        }

        $diasAtraso = Carbon::now()->diffInDays($this->data_prevista_devolucao);
        $totalFilmes = $this->filmes()->sum('locacao_filme.quantidade');
        
        return $diasAtraso * $totalFilmes * 5.00;
    }

    public function devolver(): void
    {
        $this->data_devolucao = Carbon::now();
        $this->status = 'devolvida';
        $this->multa_total = $this->calcularMulta();
        $this->save();

        foreach ($this->filmes as $filme) {
            $filme->aumentarQuantidade($filme->pivot->quantidade);
        }
    }
}
