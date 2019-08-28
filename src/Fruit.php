<?php declare(strict_types=1);

namespace App;

use JsonSerializable;

class Fruit implements JsonSerializable
{
    /** @var string */
    private $id;

    /** @var Point */
    private $position;

    public function __construct(Point $position)
    {
        $this->id = uniqid(); //FIXME: Maybe a better algo
        $this->position = $position;
    }

    public function jsonSerialize()
    {
        return [
            'fruitId' => $this->id,
            'x' => $this->position->getX(),
            'y' => $this->position->getY(),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPosition(): Point
    {
        return $this->position;
    }
}