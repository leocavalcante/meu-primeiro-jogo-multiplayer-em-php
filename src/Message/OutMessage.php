<?php declare(strict_types=1);

namespace App\Message;

use JsonSerializable;

abstract class OutMessage implements JsonSerializable
{
    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    abstract function getPayload();

    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'payload' => $this->getPayload(),
        ];
    }
}