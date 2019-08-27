<?php declare(strict_types=1);

namespace App;

class Success implements Result
{
    private $data;

    public function __construct($payload)
    {
        $this->data = $payload;
    }

    public function unwrap()
    {
        return $this->data;
    }
}