<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Data\Dto\Actions;


use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CarToBranchDto extends FlexibleDataTransferObject
{
    public String $branchId;

    public Branch $branch;

    public String $tripId;

    public Collection $storedItems;

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->branch = Branch::findOrFail($parameters['branchId']);

        $this->storedItems = StoredItem::whereIn('id', $parameters['storedItems'])->get();


    }

}
