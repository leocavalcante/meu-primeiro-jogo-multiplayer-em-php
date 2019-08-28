<?php declare(strict_types=1);

namespace App\Message\Incoming;

class Move
{
    /** @var string */
    private $direction;

    public function __construct(string $direction)
    {
        $this->direction = $direction;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}