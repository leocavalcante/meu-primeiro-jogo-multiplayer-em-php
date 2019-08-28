<?php

namespace App\Message;

class LetsGoCrazy extends InMessage
{
    public static function getType(): string
    {
        return 'admin-start-crazy-mode';
    }

    public static function parse(array $message): self
    {
        return new LetsGoCrazy();
    }
}