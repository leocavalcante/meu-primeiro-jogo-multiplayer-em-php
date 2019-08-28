<?php declare(strict_types=1);

namespace App;

use App\Direction\Down;
use App\Direction\Left;
use App\Direction\Right;
use App\Direction\Up;
use App\Message\Decoder;
use App\Message\Incoming\LetsGoCrazy;
use App\Message\Incoming\Move;
use App\Message\Incoming\Restart;
use App\Message\Incoming\SetMaxConnections;
use App\Message\Incoming\Start;
use App\Message\Incoming\Stop;
use App\Message\Incoming\StopThisMadness;
use App\Message\Outgoing\Bootstrap;
use App\Message\Outgoing\PlayerUpdate;
use App\Message\Resolver;
use App\Monad\Just;
use App\Monad\None;
use Dotenv\Dotenv;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server\Port;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use const App\Direction\Down;
use const App\Direction\Left;
use const App\Direction\Right;
use const App\Direction\Up;

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
$decoder = new Decoder();
$resolver = new Resolver();

/*
 * Arrow functions. We need you!
 */
$resolver[\App\Message\Move] = function ($payload) {
    switch (strval($payload)) {
        case Left:
            return new Move(new Left());
        case Up:
            return new Move(new Up());
        case Right:
            return new Move(new Right());
        case Down:
            return new Move(new Down());
    }
};
$resolver[\App\Message\Start] = function ($payload) {
    return new Start(intval($payload));
};
$resolver[\App\Message\Stop] = function () {
    return new Stop();
};
$resolver[\App\Message\Restart] = function () {
    return new Restart();
};
$resolver[\App\Message\LetsGoCrazy] = function () {
    return new LetsGoCrazy();
};
$resolver[\App\Message\StopThisMadness] = function () {
    return new StopThisMadness();
};
$resolver[\App\Message\SetMaxConnections] = function ($payload) {
    return new SetMaxConnections(intval($payload));
};

$server->on('open', function (Server $server, Request $request) use ($game) {
    if (count($server->connections) >= $game->getMaxConnections()) {
        $server->close($request->fd);
        return;
    }

    $player = new Player($game, $request->fd, Point::rand($game->getBounds()->getStop()));

    $game
        ->addPlayer($player)
        ->emit(new Bootstrap($request->fd, $game))
        ->emit(new PlayerUpdate($player), $request->fd);
});

$server->on('message', function (Server $_, Frame $frame) use ($game, $decoder, $resolver) {
    $message = $decoder->decode($frame->data);
    $message = $resolver->resolve($message);

    /*
     * Brace yourselves!
     * HERE COMES THE POOR MAN'S PATTERN MATCHING
     */
    if ($message instanceof None) {
        // TODO: Nothing
    }
    if ($message instanceof Just) {
        $message = $message->unwrap();
        if ($message instanceof Move) $game->move($frame->fd, $message->getDirection());
        if ($message instanceof Start) $game->start($message->getInterval());
        if ($message instanceof Stop) $game->stop();
        if ($message instanceof Restart) $game->restart();
        if ($message instanceof LetsGoCrazy) $game->letsGoCrazy();
        if ($message instanceof StopThisMadness) $game->stopThisMadness();
        if ($message instanceof SetMaxConnections) $game->setMaxConnections($message->getMaxConnections());
    }
});

$server->on('close', function (Server $_, int $fd) use ($game) {
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