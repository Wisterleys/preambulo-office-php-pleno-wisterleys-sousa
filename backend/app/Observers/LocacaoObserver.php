<?php

namespace App\Observers;

use App\Models\Locacao;
use Illuminate\Support\Facades\Cache;

class LocacaoObserver
{
    /**
     * Handle the Locacao "created" event.
     */
    public function created(Locacao $locacao): void
    {
        Cache::tags(['locacoes'])->flush();
    }

    /**
     * Handle the Locacao "updated" event.
     */
    public function updated(Locacao $locacao): void
    {
        Cache::tags(['locacoes'])->flush();
        
        // Atualizar status se estiver atrasada
        if ($locacao->estaAtrasada() && $locacao->status !== 'devolvida') {
            $locacao->status = 'atrasada';
        }
    }

    /**
     * Handle the Locacao "deleted" event.
     */
    public function deleted(Locacao $locacao): void
    {
        Cache::tags(['locacoes'])->flush();
    }

    /**
     * Handle the Locacao "restored" event.
     */
    public function restored(Locacao $locacao): void
    {
        //
    }

    /**
     * Handle the Locacao "force deleted" event.
     */
    public function forceDeleted(Locacao $locacao): void
    {
        //
    }
}
