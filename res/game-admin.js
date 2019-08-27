/* ADMIN */
function startFruitGame() {
    const interval = document.getElementById('fruitGameInterval').value;
    console.log(interval);
    socket.send(JSON.stringify({type: 'admin-start-fruit-game', payload: interval}))
}

function stopFruitGame() {
    socket.send(JSON.stringify({type: 'admin-stop-fruit-game'}))
}

function startCrazyMode() {
    socket.send(JSON.stringify({type: 'admin-start-crazy-mode'}))
}

function stopCrazyMode() {
    socket.send(JSON.stringify({type: 'admin-stop-crazy-mode'}))
}

function clearScores() {
    socket.send(JSON.stringify({type: 'admin-clear-scores'}))
}

function setMaxConcurrentConnections() {
    const maxConcurrentConnections = document.getElementById('maxConcurrentConnections').value;
    socket.send(JSON.stringify({type: 'admin-concurrent-connections', payload: maxConcurrentConnections}))
}
