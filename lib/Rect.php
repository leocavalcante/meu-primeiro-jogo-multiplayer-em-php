<?php

namespace App;

use App\Direction\Direction;
use App\Direction\Down;
use App\Direction\Left;
use App\Direction\Right;
use App\Direction\Up;

class Rect
{
    /** @var Point */
    private $p1;
    /** @var Point */
    private $p2;

    public function __construct(Point $start, Point $stop)
    {
        $this->p1 = $start;
        $this->p2 = $stop;
    }

    public function outOfBounds(Direction $direction, Point $point): bool
    {
        if ($direction instanceof Left) {
            return $point->getX() <= $this->p1->getX();
        }

        if ($direction instanceof Up) {
            return $point->getY() <= $this->p1->getY();
        }

        if ($direction instanceof Right) {
            return $point->getY() >= $this->p2->getX();
        }

        if ($direction instanceof Down) {
            return $point->getY() >= $this->p2->getY();
        }

        return false;
    }

    public function getWidth(): int
    {
        return $this->p2->getX() - $this->p1->getX();
    }

    public function getHeight(): int
    {
        return $this->p2->getY() - $this->p1->getY();
    }

    public function getStop(): Point
    {
        return $this->p2;
    }
}