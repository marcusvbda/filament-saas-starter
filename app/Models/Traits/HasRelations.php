<?php

namespace App\Models\Traits;

trait HasRelations
{
    public function syncMorphsMany(string $relationName, ?array $items): void
    {
        if ($items === null || !is_array($items)) return;

        $receivedIds = collect($items)->pluck('id')->filter()->all();

        $this->{$relationName}()->whereNotIn('id', $receivedIds)->delete();

        foreach ($items as $item) {
            if (isset($item['id'])) {
                $this->{$relationName}()->where('id', $item['id'])->update($item);
            } else {
                $this->{$relationName}()->create($item);
            }
        }
    }
}
