<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CoupleScoped
{
    public static function bootCoupleScoped(): void
    {
        static::addGlobalScope('couple', function (Builder $query) {
            $coupleId = auth()->check() ? auth()->user()->getCoupleId() : null;
            if ($coupleId) {
                $query->where('couple_id', $coupleId);
            }
        });
    }

    public function scopeForCouple(Builder $query, ?int $coupleId): Builder
    {
        if ($coupleId) {
            return $query->where('couple_id', $coupleId);
        }
        return $query;
    }
}
