<?php

namespace App\Message;

class StopGame extends InMessage
{
    public static function getType(): string
    {
        return 'admin-stop-fruit-game';
    }

    public static function parse(array $message): self
    {
        return new StopGame();
    }
}