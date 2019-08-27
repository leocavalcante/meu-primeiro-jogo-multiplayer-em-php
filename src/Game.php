<?php declare(strict_types=1);

namespace App;

use App\Message\Message;
use Swoole\WebSocket\Server;

class Game
{
    /** @var Server */
    private $server;

    /** @var int */
    private $maxConnections = 10;

    /** @var int */
    private $canvasWidth = 35;

    /** @var int */
    private $canvasHeight = 30;

    /** @var Player[] */
    private $players = [];

    /** @var array */
    private $fruits = [];

    public function __construct(Server $server, int $maxConnections = 10)
    {
        $this->server = $server;
        $this->maxConnections = $maxConnections;
    }

    public function addPlayer(int $fd): Result
    {
        // TODO: Initial random positions
        $this->players[$fd] = new Player($this, $fd);
        return new Success($this->players[$fd]);
    }

    public function removePlayer(int $fd)
    {
        unset($this->players[$fd]);
    }

    public function close(int $fd)
    {
        $this->server->close($fd);
    }

    public function emit(Message $message, int $broadcast = 0)
    {
        foreach ($this->server->connections as $connection) {
            if ($connection === $broadcast) {
                // NOTE: Broadcasting means sending a message to everyone else except for the socket that starts it.
                continue;
            }

            // FIXME: The connection isn't always a WebSocket client
            @$this->server->push($connection, json_encode($message));
        }
    }

    public function getCanvasWidth(): int
    {
        return $this->canvasWidth;
    }

    public function getCanvasHeight(): int
    {
        return $this->canvasHeight;
    }

    public function getState(): array
    {
        return [
            'canvasWidth' => $this->getCanvasWidth(),
            'canvasHeight' => $this->getCanvasHeight(),
            'players' => $this->players,
            'fruits' => $this->fruits,
        ];
    }

    public function movePlayer(int $fd, $payload): Player
    {
        $player = $this->players[$fd];
        $player->move($payload);
        return $player;
    }

    public function getServer(): Server
    {
        return $this->server;
    }
}