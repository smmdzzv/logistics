<?php


namespace App\Data\RequestWriters;


use stdClass;

abstract class RequestWriter
{
    protected $input;
    protected $data;
    protected $saved;

    protected function __construct($input)
    {
        $this->input = $input;
        $this->saved = new stdClass();
        $this->data = new stdClass();
    }

    abstract function write();
}
