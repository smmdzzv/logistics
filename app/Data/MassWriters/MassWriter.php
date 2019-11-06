<?php


namespace App\Data\MassWriters;


use Illuminate\Support\Carbon;
use Rorecek\Ulid\Ulid;

abstract class MassWriter
{
    protected $entities;
    protected $data;
    protected $entityClass;
    private $ulidGenerator;

    public function __construct(Array $entities)
    {
        $this->ulidGenerator = new Ulid();
        $this->entities = $entities;
        $this->prepare();
    }

    /**
     * Converts models to array of attributes with generated ULID and created_at
     */
    private function prepare()
    {
        foreach ($this->entities as $entity) {
            $entity->id = $this->ulidGenerator->generate();
            $entity->created_at = Carbon::now();
            $this->data[] = $entity->attributesToArray();
        };
    }

    /**
     * @return array of saved models
     */
    public function write()
    {
        $this->entityClass::insert($this->data);
        return $this->entities;
    }
}
