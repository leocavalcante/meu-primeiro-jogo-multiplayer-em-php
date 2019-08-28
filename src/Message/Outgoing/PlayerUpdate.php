<?php declare(strict_types=1);

namespace App\Message\Outgoing;

use App\Message\Outgoing;
use App\Player;

class PlayerUpdate extends Outgoing
{
    /** @var Player */
    private $player;

    public function __construct(Player $player)
    {
        parent::__construct(PlayerUpdate);
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