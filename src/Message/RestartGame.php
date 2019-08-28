<?php

namespace App\Message;

class RestartGame extends InMessage
{
    public static function getType(): string
    {
        return 'admin-clear-scores';
    }

    public static function parse(array $message): self
    {
        return new RestartGame();
    }
}