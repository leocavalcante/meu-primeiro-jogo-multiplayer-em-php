<?php declare(strict_types=1);

namespace App;

use App\Message\Bootstrap;
use App\Message\PlayerMove;
use App\Message\PlayerUpdate;
use App\Message\StartGame;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Port;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

$basedir = __DIR__ . "/..";
require_once "$basedir/vendor/autoload.php";

$max_connections = isset($argv[1]) ? intval($argv[1]) : 10;

$server = new Server('0.0.0.0', 9001);
$server->set(['worker_num' => 1]);

$game = new Game($server);

$server->on('open', function (Server $server, Request $request) use ($max_connections, $game) {
    if (count($server->connections) >= $max_connections) {
        $server->close($request->fd);
        return;
    }

    $player = new Player($game, $request->fd, new Point2D());

    $game->addPlayer($player)
        ->emit(new Bootstrap($request->fd, $game))
        ->emit(new PlayerUpdate($player), $request->fd);
});

$server->on('message', function (Server $server, Frame $frame) use ($game) {
    $message = json_decode($frame->data, true);

    switch ($message['type']) {
        case PlayerMove::getType():
            $message = PlayerMove::parse($message);
            $player = $game->getPlayerById($frame->fd)->move($message->getDirection());
            $game->checkForCollisions()->emit(new PlayerUpdate($player), $frame->fd);
            break;

        case StartGame::getType():
            $message = StartGame::parse($message);
            $game->start($message->getInterval());
            break;

//          TODO

//          socket.on('admin-stop-fruit-game', () => {
//                    console.log('> Fruit Game stop')
//            clearInterval(fruitGameInterval)
//          })
//
//          socket.on('admin-start-crazy-mode', () => {
//                    io.emit('start-crazy-mode')
//          })
//
//          socket.on('admin-stop-crazy-mode', () => {
//                    io.emit('stop-crazy-mode')
//          })
//
//          socket.on('admin-clear-scores', () => {
//                    game.clearScores()
//            io.emit('bootstrap', game)
//          })
//
//          socket.on('admin-concurrent-connections', (newConcurrentConnections) => {
//                    maxConcurrentConnections = newConcurrentConnections
//          })

    }
});

$server->on('close', function (Server $server, int $fd) use ($game) {
    $game->removePlayer($fd);
});

/** @var Port $http */
$http = $server->listen('0.0.0.0', 9000, SWOOLE_SOCK_TCP);
$http->set(['open_http_protocol' => true]);
$http->on('request', function (Request $request, Response $response) use ($basedir) {
    switch ($request->server['request_uri']) {
        case '/':
            $response->sendfile("$basedir/res/game.html");
            break;

        case '/a31ecc0596d72f84e5ee403ddcacb3dea94ce0803fc9e6dc2eca1fbabae49a3e3a31ecc0596d72f84e5ee40d0cacb3dea94ce0803fc9e6dc2ecfdfdbabae49a3e3':
            $game_html = file_get_contents("$basedir/res/game.html");
            $admin_html = file_get_contents("$basedir/res/game-admin.html");
            $game_html = str_replace('<!-- admin -->', $admin_html, $game_html);
            $response->end($game_html);
            break;

        case '/collect.mp3':
        case '/100-collect.mp3':
        case '/game.js':
        case '/game.css':
        case '/game-admin.js':
            $response->sendfile("$basedir/res/{$request->server['request_uri']}");
            break;

        default:
            $response->status(404);
            $response->end();
            break;
    }
});

$server->start();