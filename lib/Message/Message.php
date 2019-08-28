<?php

namespace App\Message;

class Message
{
    /** @var string */
    private $type;
    private $payload;

    public function __construct(string $type, $payload = null)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}