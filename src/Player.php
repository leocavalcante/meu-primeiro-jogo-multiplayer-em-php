<?php declare(strict_types=1);

namespace App;

use JsonSerializable;

class Player implements JsonSerializable
{

    /** @var Game */
    private $game;

    /** @var int */
    private $id;

    /** @var int */
    private $score;

    /** @var Point2D */
    private $position;

    public function __construct(Game $game, int $id, Point2D $position)
    {
        $this->game = $game;
        $this->id = $id;
        $this->score = 0;
        $this->position = $position;
    }

    public function move(string $direction): self
    {
        switch ($direction) {
            case 'left':
                if ($this->position->getX() - 1 >= 0) {
                    $this->position->decreaseX();
                }
                break;

            case 'up':
                if ($this->position->getY() - 1 >= 0) {
                    $this->position->decreaseY();
                }
                break;

            case 'right':
                if ($this->position->getX() + 1 < $this->game->getCanvasWidth()) {
                    $this->position->increaseX();

                }
                break;

            case 'down':
                if ($this->position->getY() + 1 < $this->game->getCanvasHeight()) {
                    $this->position->increaseY();
                }
                break;
        }

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