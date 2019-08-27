<?php declare(strict_types=1);

namespace App;

class Point2D
{
    /** @var int */
    private $x;
    /** @var int $y */
    private $y;

    public function __construct(int $x = 0, int $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function overlaps(Point2D $point): bool
    {
        return ($this->x === $point->x) && ($this->y === $point->y);
    }

    public function decreaseX()
    {
        $this->x -= 1;
    }

    public function decreaseY()
    {
        $this->y -= 1;
    }

    public function increaseX()
    {
        $this->x += 1;
    }

    public function increaseY()
    {
        $this->y += 1;
    }
}