<?php

namespace App\Observers;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PessoaObserver
{
    /**
     * Handle the Pessoa "created" event.
     */
    public function creating(Pessoa $pessoa): void
    {
        // Criar User automaticamente se não existir user_id
        if (empty($pessoa->user_id)) {
            $user = User::create([
                'name' => $pessoa->nome_completo,
                'email' => $this->gerarEmailTemporario($pessoa),
                'password' => Hash::make(Str::random(16)),
            ]);
            $pessoa->user_id = $user->id;
        }
    }

    public function created(Pessoa $pessoa): void
    {
        Cache::tags(['pessoas'])->flush();
    }

    /**
     * Handle the Pessoa "updated" event.
     */
    public function updated(Pessoa $pessoa): void
    {
        Cache::tags(['pessoas'])->flush();
        
        // Atualizar nome do user se mudou
        if ($pessoa->isDirty('nome_completo')) {
            $pessoa->user->update(['name' => $pessoa->nome_completo]);
        }
    }

    /**
     * Handle the Pessoa "deleted" event.
     */
    public function deleted(Pessoa $pessoa): void
    {
        Cache::tags(['pessoas'])->flush();
    }

    /**
     * Handle the Pessoa "restored" event.
     */
    public function restored(Pessoa $pessoa): void
    {
        //
    }

    /**
     * Handle the Pessoa "force deleted" event.
     */
    public function forceDeleted(Pessoa $pessoa): void
    {
        //
    }

    /**
     * Gerar email temporário baseado no CPF ou nome
     */
    private function gerarEmailTemporario(Pessoa $pessoa): string
    {
        if ($pessoa->cpf) {
            return 'user_' . preg_replace('/\D/', '', $pessoa->cpf) . '@preambulo.temp';
        }
        return 'user_' . Str::slug($pessoa->nome_completo) . '_' . time() . '@preambulo.temp';
    }
}
