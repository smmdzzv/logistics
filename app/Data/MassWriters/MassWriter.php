<?php


namespace App\Data\MassWriters;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

abstract class MassWriter
{
    protected $entities;
    protected array $data;
    protected $entityClass;

    public function __construct(Array $entities)
    {
        $this->entities = $entities;
        $this->data = [];
        $this->prepare();
    }

    /**
     * Converts models to array of attributes with generated ULID and created_at
     */
    private function prepare()
    {
        foreach ($this->entities as $entity) {
            $entity->id = (string) Str::orderedUuid();
            $entity->created_at = Date::now();
            $entity->created_by_id = auth()->id();
            $this->data[] = $entity->attributesToArray();
        };
    }

    /**
     * @return array of saved models
     */
    public function write()
    {
        if (count($this->data) === 0)
            return [];
        $this->entityClass::insert($this->data);
        return $this->entities;
    }
}
