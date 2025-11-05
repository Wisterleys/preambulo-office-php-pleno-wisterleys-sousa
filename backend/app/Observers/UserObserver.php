<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        //
    }

    public function created(User $user): void
    {
        if ($user->pessoa()->exists()) {
            return;
        }

        Pessoa::create([
            'user_id' => $user->id,
            'nome_completo' => $user->name,
            'cpf' => null,
            'data_nascimento' => null,
            'endereco' => null,
            'foto_perfil' => null,
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
       //
    }

    public function deleting(User $user): void
    {
        if ($user->pessoa) {
            $user->pessoa->forceDelete();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
       //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
