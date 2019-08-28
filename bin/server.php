<?php declare(strict_types=1);

namespace App;

use App\Message\Bootstrap;
use App\Message\LetsGoCrazy;
use App\Message\PlayerMove;
use App\Message\PlayerUpdate;
use App\Message\RestartGame;
use App\Message\SetMaxConnections;
use App\Message\StartGame;
use App\Message\StopGame;
use App\Message\StopThisMadness;
use Dotenv\Dotenv;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Port;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

$basedir = __DIR__ . "/..";
require_once "$basedir/vendor/autoload.php";

if (file_exists("$basedir/.env")) {
    Dotenv::create($basedir)->load();
}

$max_connections = getenv('MAX_CONNECTIONS') ? intval(getenv('MAX_CONNECTIONS')) : 10;
$websocket_port = getenv('WEBSOCKET_PORT') ? intval(getenv('WEBSOCKET_PORT')) : 9001;
$http_port = getenv('HTTP_PORT') ? intval(getenv('HTTP_PORT')) : 9000;

$server = new Server('0.0.0.0', $websocket_port);
$server->set(['worker_num' => 1]);

$game = new Game($server, $max_connections);

$server->on('open', function (Server $server, Request $request) use ($game) {
    if (count($server->connections) >= $game->getMaxConnections()) {
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

        case StopGame::getType():
            $game->stop();
            break;

        case RestartGame::getType():
            $game->restart();
            break;

        case LetsGoCrazy::getType():
            $game->letsGoCrazy();
            break;

        case StopThisMadness::getType():
            $game->stopThisMadness();
            break;

        case SetMaxConnections::getType():
            $message = SetMaxConnections::parse($message);
            $game->setMaxConnections($message->getValue());
            break;

    }
});

$server->on('close', function (Server $server, int $fd) use ($game) {
    $game->removePlayer($fd);
});

/** @var Port $http */
$http = $server->listen('0.0.0.0', $http_port, SWOOLE_SOCK_TCP);
$http->set(['open_http_protocol' => true]);
$http->on('request', function (Request $request, Response $response) use ($basedir) {
    switch ($request->server['request_uri']) {
        case '/':
            $response->sendfile("$basedir/res/game.html");
            break;

        case '/' . getenv('ADMIN_KEY'):
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