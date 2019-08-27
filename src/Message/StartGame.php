<?php declare(strict_types=1);

namespace App\Message;

class StartGame extends InMessage
{
    /** @var int */
    private $interval;

    public function __construct(int $interval)
    {
        $this->interval = $interval;
    }

    public static function getType(): string
    {
        return 'admin-start-fruit-game';
    } // ms

    public static function parse(array $message): StartGame
    {
        return new StartGame(intval($message['payload']));
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}