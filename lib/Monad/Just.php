<?php

namespace App\Monad;

class Just implements Maybe
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function unwrap()
    {
        return $this->value;
    }
}