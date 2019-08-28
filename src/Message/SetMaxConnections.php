<?php

namespace App\Message;

class SetMaxConnections extends InMessage
{

    /** @var int */
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function getType(): string
    {
        return 'admin-concurrent-connections';
    }

    public static function parse(array $message)
    {
        return new SetMaxConnections(intval($message['payload']));
    }

    public function getValue(): int
    {
        return $this->value;
    }
}