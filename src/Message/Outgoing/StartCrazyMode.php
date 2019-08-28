<?php

namespace App\Message\Outgoing;

use App\Message\Outgoing;

class StartCrazyMode extends Outgoing
{
    public function __construct()
    {
        parent::__construct(StartCrazyMode);
    }

    function getPayload(): array
    {
        return [];
    }
}