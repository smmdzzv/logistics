<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 11.07.2020
 */

namespace App\Services\Selection;


use App\Models\StoredItems\ItemsSelection;
use App\User;
use Illuminate\Support\Collection;

class ItemsSelectionService
{
    public function store(User $user, Collection $storedItemsIds, string $name): ItemsSelection
    {
        /** @var ItemsSelection $selection */
        $selection = $user->itemsSelection()->create(['name' => $name]);
        $selection->storedItems()->sync($storedItemsIds);
        return $selection;
    }
}
