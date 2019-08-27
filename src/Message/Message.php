<?php declare(strict_types=1);

namespace App\Message;

abstract class Message implements \JsonSerializable
{
    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    abstract function getPayload(): array;

    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'payload' => $this->getPayload(),
        ];
    }
}