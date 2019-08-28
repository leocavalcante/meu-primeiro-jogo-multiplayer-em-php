<?php declare(strict_types=1);

namespace App\Message\Outgoing;

use App\Message\Outgoing;
use App\Player;

class UpdatePlayerScore extends Outgoing
{
    /** @var Player */
    private $player;

    public function __construct(Player $player)
    {
        parent::__construct(UpdatePlayerScore);
        $this->player = $player;
    }

    function getPayload(): int
    {
        return $this->player->getScore();
    }
}