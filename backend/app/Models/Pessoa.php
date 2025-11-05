<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pessoa extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'pessoas';

    protected $fillable = [
        'user_id',
        'nome_completo',
        'cpf',
        'data_nascimento',
        'endereco',
        'foto_perfil',
        'role',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    protected $hidden = [
        'id',
        'user_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locacoes(): HasMany
    {
        return $this->hasMany(Locacao::class);
    }

    public function isMaiorDeIdade(): bool
    {
        return $this->data_nascimento->age >= 18;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
