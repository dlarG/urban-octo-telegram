<?php
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Deactivatable
{
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeInactive(Builder $q): Builder
    {
        return $q->where('is_active', false);
    }

    public function deactivate(): void
    {
        $this->forceFill(['is_active' => false])->save();
    }

    public function reactivate(): void
    {
        $this->forceFill(['is_active' => true])->save();
    }

    /**
     * Hard-delete is only allowed when the model has zero historical activity.
     * Override in models that have activity to define what "activity" means.
     */
    public function canBeHardDeleted(): bool
    {
        return true;
    }
}