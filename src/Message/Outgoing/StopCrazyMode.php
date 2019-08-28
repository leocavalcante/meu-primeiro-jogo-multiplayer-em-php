<?php

namespace App\Message\Outgoing;

use App\Message\Outgoing;

class StopCrazyMode extends Outgoing
{
    public function __construct()
    {
        parent::__construct(StopCrazyMode);
    }

    function getPayload(): array
    {
        return [];
    }
}