<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            $model->logActivity('created', [], $model->toArray());
        });

        static::updated(function (Model $model) {
            $dirty = $model->getDirty();
            $original = $model->getOriginal();

            $oldValues = [];
            $newValues = [];
            foreach ($dirty as $key => $value) {
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $value;
            }

            if (!empty($oldValues)) {
                $model->logActivity('updated', $oldValues, $newValues);
            }
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted', $model->toArray(), []);
        });
    }

    public function logActivity(string $event, array $oldValues, array $newValues): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
