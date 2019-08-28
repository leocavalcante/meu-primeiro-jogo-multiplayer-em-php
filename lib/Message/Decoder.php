<?php

namespace App\Message;

use Webmozart\Assert\Assert;

class Decoder
{
    public function decode(string $message): Message
    {
        $data = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

        Assert::keyExists($data, 'type');
        Assert::string($data['type']);

        return new Message($data['type'], $data['payload'] ?? null);
    }
}