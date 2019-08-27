<?php declare(strict_types=1);

namespace App\Message;

use App\Player;

class PlayerUpdate extends Message
{
    /** @var Player */
    private $player;

    public function __construct(Player $player)
    {
        parent::__construct('player-update');
        $this->player = $player;
    }

    function getPayload(): array
    {
        return [
            'socketId' => $this->player->getId(),
            'newState' => $this->player,
        ];
    }
}