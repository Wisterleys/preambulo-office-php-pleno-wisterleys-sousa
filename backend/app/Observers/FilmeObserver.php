<?php

namespace App\Observers;

use App\Models\Filme;
use Illuminate\Support\Facades\Cache;

class FilmeObserver
{
    /**
     * Handle the Filme "created" event.
     */
    public function created(Filme $filme): void
    {
        $this->invalidarCache();
    }

    /**
     * Handle the Filme "updated" event.
     */
    public function updated(Filme $filme): void
    {
        $this->invalidarCache();
    }

    /**
     * Handle the Filme "deleted" event.
     */
    public function deleted(Filme $filme): void
    {
        $this->invalidarCache();
    }

    /**
     * Handle the Filme "restored" event.
     */
    public function restored(Filme $filme): void
    {
        $this->invalidarCache();
    }

    /**
     * Handle the Filme "force deleted" event.
     */
    public function forceDeleted(Filme $filme): void
    {
        $this->invalidarCache();
    }

    private function invalidarCache(): void
    {
        Cache::tags(['filmes', 'catalogo'])->flush();
    }
}
