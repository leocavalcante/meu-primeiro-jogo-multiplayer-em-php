<?php declare(strict_types=1);

namespace App;

class Player implements \JsonSerializable
{
    /** @var Game */
    private $game;

    /** @var int */
    private $id;

    /** @var int */
    private $score;

    /** @var int */
    private $x;

    /** @var int */
    private $y;

    public function __construct(Game $game, int $id, int $x = 0, int $y = 0)
    {
        $this->game = $game;
        $this->id = $id;
        $this->score = 0;
        $this->x = $x;
        $this->y = $y;
    }

    public function move(string $direction)
    {
        switch ($direction) {
            case 'left':
                if ($this->x - 1 >= 0) {
                    $this->x -= 1;
                }
                break;

            case 'up':
                if ($this->y - 1 >= 0) {
                    $this->y -= 1;
                }
                break;

            case 'right':
                if ($this->x + 1 < $this->game->getCanvasWidth()) {
                    $this->x += 1;

                }
                break;

            case 'down':
                if ($this->y + 1 < $this->game->getCanvasHeight()) {
                    $this->y += 1;
                }
                break;
        }
    }

    public function jsonSerialize()
    {
        return [
            'score' => $this->score,
            'x' => $this->x,
            'y' => $this->y,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
}