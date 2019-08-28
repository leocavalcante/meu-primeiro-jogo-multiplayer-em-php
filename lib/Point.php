<?php declare(strict_types=1);

namespace App;

class Point
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

    public function overlaps(Point $point): bool
    {
        return ($this->x === $point->x) && ($this->y === $point->y);
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function decX()
    {
        $this->x -= 1;
    }

    public function decY()
    {
        $this->y -= 1;
    }

    public function incX()
    {
        $this->x += 1;
    }

    public function incY()
    {
        $this->y += 1;
    }
}