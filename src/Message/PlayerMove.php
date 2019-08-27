<?php declare(strict_types=1);

namespace App\Message;

class PlayerMove extends InMessage
{
    /** @var string */
    private $direction;

    public function __construct(string $direction)
    {
        $this->direction = $direction;
    }

    public static function getType(): string
    {
        return 'player-move';
    }

    public static function parse(array $message): self
    {
        return new PlayerMove(strval($message['payload']));
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}