<?php


namespace App\Models;


abstract class SerializableModel
{
    abstract function toArray();

    public function toJson()
    {
        return json_encode($this->toArray(), 0);
    }
}
