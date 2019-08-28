<?php declare(strict_types=1);

namespace App;

use App\Message\Outgoing;
use App\Message\Outgoing\Bootstrap;
use App\Message\Outgoing\FruitAdded;
use App\Message\Outgoing\FruitRemoved;
use App\Message\Outgoing\PlayerUpdate;
use App\Message\Outgoing\StartCrazyMode;
use App\Message\Outgoing\StopCrazyMode;
use App\Message\Outgoing\UpdatePlayerScore;
use Swoole\Timer;
use Swoole\WebSocket\Server;

class Game
{
    /** @var Server */
    private $server;

    /** @var int */
    private $maxConnections = 10;

    /** @var int */
    private $width = 35;

    /** @var int */
    private $height = 30;

    /** @var Player[] */
    private $players = [];

    /** @var array */
    private $fruits = [];

    /** @var int */
    private $tick;

    public function __construct(Server $server, int $maxConnections = 10)
    {
        $this->server = $server;
        $this->maxConnections = $maxConnections;
    }

    public function addPlayer(Player $player): self
    {
        $this->players[$player->getId()] = $player;
        return $this;
    }

    public function removePlayer(int $playerId)
    {
        unset($this->players[$playerId]);
    }

    public function emit(Outgoing $message, int $broadcast = 0): self
    {
        foreach ($this->server->connections as $connection) {
            if ($connection === $broadcast) {
                // NOTE: Broadcasting means sending a message to everyone else except for the socket that starts it.
                continue;
            }

            // FIXME: The connection isn't always a WebSocket client
            @$this->server->push($connection, json_encode($message));
        }

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getState(): array
    {
        return [
            'canvasWidth' => $this->getWidth(),
            'canvasHeight' => $this->getHeight(),
            'players' => $this->players,
            'fruits' => $this->fruits,
        ];
    }

    public function getPlayerById(int $playerId): Player
    {
        return $this->players[$playerId];
    }

    public function start(int $interval)
    {
        Timer::clear($this->tick);
        $this->tick = $this->server->tick($interval, [$this, 'onTick']);
    }

    public function onTick()
    {
        $fruit = $this->addFruit(new Fruit($this->randomPosition()));
        $this->emit(new FruitAdded($fruit));
    }

    public function move(int $playerId, string $direction): self
    {
        $player = $this->getPlayerById($playerId)->move($direction);
        $this->checkForCollisions();
        $this->emit(new PlayerUpdate($player), $playerId);
        return $this;
    }

    public function checkForCollisions(): self
    {
        // TODO: A better algo
        foreach ($this->players as $player) {
            foreach ($this->fruits as $fruit) {
                if ($player->hits($fruit)) {
                    $player->scores();
                    $this->removeFruit($fruit);
                    $this->emit(new FruitRemoved($fruit));
                    $this->emit(new UpdatePlayerScore($player));
                }
            }
        }

        return $this;
    }

    private function addFruit(Fruit $fruit): Fruit
    {
        $this->fruits[$fruit->getId()] = $fruit;
        return $fruit;
    }

    private function removeFruit(Fruit $fruit)
    {
        unset($this->fruits[$fruit->getId()]);
    }

    public function randomPosition(): Point
    {
        return new Point(rand(0, $this->getWidth()), rand(0, $this->getHeight()));
    }

    public function stop()
    {
        Timer::clear($this->tick);
    }

    public function getMaxConnections(): int
    {
        return $this->maxConnections;
    }

    public function letsGoCrazy()
    {
        $this->emit(new StartCrazyMode());
    }

    public function stopThisMadness()
    {
        $this->emit(new StopCrazyMode());
    }

    public function restart()
    {
        foreach ($this->players as $player) {
            $player->reset();
        }

        $this->emit(new Bootstrap(0, $this));
    }

    public function setMaxConnections(int $maxConnections)
    {
        $this->maxConnections = $maxConnections;
    }
}