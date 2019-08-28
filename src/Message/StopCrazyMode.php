<?php

namespace App\Message;

class StopCrazyMode extends OutMessage
{
    public function __construct()
    {
        parent::__construct('stop-crazy-mode');
    }

    function getPayload(): array
    {
        return [];
    }
}