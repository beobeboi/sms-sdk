<?php

namespace DiagVN\Services\Fpt\TechAPI;

class Exception extends \Exception
{
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
