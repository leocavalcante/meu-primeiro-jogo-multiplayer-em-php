<?php

namespace App\Message;

class StartCrazyMode extends OutMessage
{
    public function __construct()
    {
        parent::__construct('start-crazy-mode');
    }

    function getPayload(): array
    {
        return [];
    }
}