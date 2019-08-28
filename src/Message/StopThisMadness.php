<?php

namespace App\Message;

class StopThisMadness extends InMessage
{
    public static function getType(): string
    {
        return 'admin-stop-crazy-mode';
    }

    public static function parse(array $message): self
    {
        return new StopThisMadness();
    }
}