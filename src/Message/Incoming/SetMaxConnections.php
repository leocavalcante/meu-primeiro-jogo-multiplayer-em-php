<?php

namespace App\Message\Incoming;

class SetMaxConnections
{
    /** @var int */
    private $maxConnections;

    public function __construct(int $maxConnections)
    {
        $this->maxConnections = $maxConnections;
    }

    public function getMaxConnections(): int
    {
        return $this->maxConnections;
    }
}