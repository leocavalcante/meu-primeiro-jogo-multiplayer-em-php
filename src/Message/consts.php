<?php

namespace App\Message;

// Incoming messages
const Move = 'player-move';
const Start = 'admin-start-fruit-game';
const Stop = 'admin-stop-fruit-game';
const Restart = 'admin-clear-scores';
const LetsGoCrazy = 'admin-start-crazy-mode';
const StopThisMadness = 'admin-stop-crazy-mode';
const SetMaxConnections = 'admin-concurrent-connections';

// Outgoing messages
const Bootstrap = 'bootstrap';
const FruitAdded = 'fruit-add';
const FruitRemoved = 'fruit-remove';
const PlayerUpdate = 'player-update';
const StartCrazyMode = 'start-crazy-mode';
const StopCrazyMode = 'stop-crazy-mode';
const UpdatePlayerScore = 'update-player-score';