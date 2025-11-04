<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootUuidTrait(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::uuid4()->toString();
            }
        });
    }
}
