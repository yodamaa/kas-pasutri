<?php

namespace App\Filament\Support;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class PeruntukanOptions
{
    public const CACHE_KEY = 'peruntukan-options';

    public const TTL = 3600;

    public static function for(?string $tipe): array
    {
        $map = self::allByTipe();

        if ($tipe !== null) {
            return $map[$tipe] ?? [];
        }

        return array_merge(...array_values($map));
    }

    public static function allByTipe(): array
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function (): array {
            return Category::query()
                ->where('is_active', true)
                ->orderBy('nama')
                ->get()
                ->groupBy(fn (Category $category): string => $category->tipe ?: 'lainnya')
                ->map(fn ($items) => $items->pluck('nama', 'id')->all())
                ->all();
        });
    }

    public static function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
