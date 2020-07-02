<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace App\Services\Item;

use App\Data\Dto\Item\ItemDto;
use App\Models\StoredItems\Item;

class ItemService
{
    public function store(ItemDto $dto): Item
    {
        $item = Item::create($dto->except('customsCodes')->toArray());

        /** @var Item $item */
        $item->codes()->attach($dto->customsCodes);

        return $item;
    }

    public function update(Item $item, ItemDto $dto): Item
    {
        $item->update($dto->except('customsCodes')->toArray());

        $item->codes()->sync($dto->customsCodes);

        return $item;
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return;
    }
}
