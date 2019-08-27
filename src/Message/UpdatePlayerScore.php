<?php declare(strict_types=1);

namespace App\Message;

use App\Player;

class UpdatePlayerScore extends OutMessage
{
    /** @var Player */
    private $player;

    public function __construct(Player $player)
    {
        parent::__construct('update-player-score');
        $this->player = $player;
    }

    function getPayload(): int
    {
        return $this->player->getScore();
    }
}