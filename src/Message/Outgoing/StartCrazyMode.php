<?php

namespace App\Message\Outgoing;

use App\Message\Outgoing;
use const App\Message\StartCrazyMode;

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