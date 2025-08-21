#!/bin/bash
set -e
echo "--- Running Composer Install ---"
composer install
echo "--- Starting WebSocket Daemon ---"
php bin/ssh-ws-daemon.php > daemon.log 2>&1 &
DAEMON_PID=$!
echo "Daemon started with PID $DAEMON_PID"
sleep 3
echo "--- Connecting with wscat ---"
echo "ls -l" | wscat -c ws://127.0.0.1:8080
echo "--- wscat finished ---"
sleep 2
echo "--- Killing Daemon ---"
kill $DAEMON_PID
echo "--- Daemon Log ---"
cat daemon.log
