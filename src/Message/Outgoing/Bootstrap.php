<?php declare(strict_types=1);

namespace App\Message\Outgoing;

use App\Game;
use App\Message\Outgoing;
use const App\Message\Bootstrap;

class Bootstrap extends Outgoing
{
    /** @var int */
    private $playerId;

    /** @var Game */
    private $game;

    public function __construct(int $playerId, Game $game)
    {
        parent::__construct(Bootstrap);
        $this->playerId = $playerId;
        $this->game = $game;
    }

    function getPayload(): array
    {
        return array_merge(['socketId' => $this->playerId], $this->game->getState());
    }
}