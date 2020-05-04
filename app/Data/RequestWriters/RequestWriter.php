<?php


namespace App\Data\RequestWriters;


use PhpParser\Node\Expr\Cast\Object_;
use stdClass;

abstract class RequestWriter
{
    protected $request;

    /**
     * @deprecated
     * Holds input data
     *
     * @var stdClass|array
     */
    protected $input;

    /**
     *@deprecated
     *Object that holds intermediate data required for saving models (mostly relations)
     *
     * @var stdClass
     */
    protected $data;

    /**
     * @deprecated
     * Object that holds data that was saved by RequestWriter
     *
     * @var stdClass
     */
    protected $saved;

    public function __construct($input, $request = null)
    {
        $this->input = $input;
        $this->saved = new stdClass();
        $this->data = new stdClass();
        $this->request = $request;
    }

    /**
     * @return stdClass which contains saved models
     */
    abstract function write();
}
