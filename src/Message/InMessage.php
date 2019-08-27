<?php declare(strict_types=1);

namespace App\Message;

abstract class InMessage
{
    abstract public static function getType(): string;

    abstract public static function parse(array $message);
}