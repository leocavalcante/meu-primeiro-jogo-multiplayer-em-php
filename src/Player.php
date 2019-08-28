<?php declare(strict_types=1);

namespace App;

use App\Direction\Direction;
use App\Direction\Down;
use App\Direction\Left;
use App\Direction\Right;
use App\Direction\Up;
use JsonSerializable;

class Player implements JsonSerializable
{

    /** @var Game */
    private $game;

    /** @var int */
    private $id;

    /** @var int */
    private $score;

    /** @var Point */
    private $position;

    public function __construct(Game $game, int $id, Point $position)
    {
        $this->game = $game;
        $this->id = $id;
        $this->score = 0;
        $this->position = $position;
    }

    public function move(Direction $direction): self
    {
        if ($this->game->getBounds()->outOfBounds($direction, $this->position)) {
            return $this;
        }

        if ($direction instanceof Left) $this->position->decX();
        if ($direction instanceof Up) $this->position->decY();
        if ($direction instanceof Right) $this->position->incX();
        if ($direction instanceof Down) $this->position->incY();

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'score' => $this->score,
            'x' => $this->position->getX(),
            'y' => $this->position->getY(),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function scores()
    {
        $this->score += 1;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function hits(Fruit $fruit): bool
    {
        return $this->position->overlaps($fruit->getPosition());
    }

    public function reset()
    {
        $this->score = 0;
    }
}