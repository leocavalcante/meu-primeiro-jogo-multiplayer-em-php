<?php declare(strict_types=1);

namespace App;

use App\Message\Bootstrap;
use App\Message\FruitAdded;
use App\Message\FruitRemoved;
use App\Message\OutMessage;
use App\Message\StartCrazyMode;
use App\Message\StopCrazyMode;
use App\Message\UpdatePlayerScore;
use Swoole\Timer;
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

    public function removePlayer(int $fd)
    {
        unset($this->players[$fd]);
    }

    public function emit(OutMessage $message, int $broadcast = 0): self
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

    public function getPlayerById(int $id): Player
    {
        return $this->players[$id];
    }

    public function start(int $interval)
    {
        Timer::clear($this->tick);
        $this->tick = $this->server->tick($interval, [$this, 'onTick']);
    }

    public function onTick()
    {
        $fruit = new Fruit($this->randomPosition());
        $this->fruits[$fruit->getId()] = $fruit;
        $this->emit(new FruitAdded($fruit));
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

    private function removeFruit(Fruit $fruit)
    {
        unset($this->fruits[$fruit->getId()]);
    }

    public function randomPosition(): Point2D
    {
        return new Point2D(rand(0, $this->getCanvasWidth()), rand(0, $this->getCanvasHeight()));
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