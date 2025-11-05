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
        //
    }

    public function created(Pessoa $pessoa): void
    {
        //
    }

    /**
     * Handle the Pessoa "updated" event.
     */
    public function updated(Pessoa $pessoa): void
    {
        //
    }

    /**
     * Handle the Pessoa "deleted" event.
     */
    public function deleted(Pessoa $pessoa): void
    {
        //
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
}
