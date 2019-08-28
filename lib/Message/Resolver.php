<?php

namespace App\Message;

use App\Monad\Just;
use App\Monad\Maybe;
use App\Monad\None;
use ArrayAccess;

class Resolver implements ArrayAccess
{
    /** @var Handler[] */
    private $handlers = [];

    public function resolve(Message $message): Maybe
    {
        $type = $message->getType();

        if (!array_key_exists($type, $this->handlers)) {
            return new None();
        }

        return new Just($this->handlers[$type]($message->getPayload()));
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->handlers);
    }

    public function offsetGet($offset)
    {
        return $this->handlers[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->handlers[$offset] = new Handler($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->handlers[$offset]);
    }
}