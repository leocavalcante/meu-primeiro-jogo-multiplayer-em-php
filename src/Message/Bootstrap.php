<?php declare(strict_types=1);

namespace App\Message;

use App\Game;

class Bootstrap extends Message
{
    /** @var int */
    private $fd;

    /** @var Game */
    private $game;

    public function __construct(int $fd, Game $game)
    {
        parent::__construct('bootstrap');
        $this->fd = $fd;
        $this->game = $game;
    }

    function getPayload(): array
    {
        return array_merge(['socketId' => $this->fd], $this->game->getState());
    }
}