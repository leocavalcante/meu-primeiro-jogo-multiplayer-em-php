<?php declare(strict_types=1);

namespace App\Message\Incoming;

use App\Direction\Direction;

class Move
{
    /** @var Direction */
    private $direction;

    public function __construct(Direction $direction)
    {
        $this->direction = $direction;
    }

    public function getDirection(): Direction
    {
        return $this->direction;
    }
}