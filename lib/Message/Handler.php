<?php

namespace App\Message;

class Handler
{
    /** @var string */
    private $name;

    /** @var callable */
    private $factory;

    public function __construct(string $name, callable $factory)
    {
        $this->name = $name;
        $this->factory = $factory;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __invoke($payload = null)
    {
        $factory = $this->factory;
        return $factory($payload);
    }
}