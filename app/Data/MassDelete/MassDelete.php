<?php


namespace App\Data\MassDelete;


use Carbon\Carbon;

class MassDelete
{
    protected $ids;
    protected $entityClass;
    protected $deletedBy;

    public function __construct(Array $ids, $entityClass, $deletedBy)
    {
        $this->ids = $ids;
        $this->entityClass = $entityClass;
        $this->deletedBy = $deletedBy;
    }

    public function delete()
    {
        $this->entityClass::whereIn('id', $this->ids)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => $this->deletedBy
        ]);
    }
}
