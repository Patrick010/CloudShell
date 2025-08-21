document.addEventListener('DOMContentLoaded', function() {
    const terminalContainer = document.getElementById('terminal');
    if (!terminalContainer) {
        console.error("Terminal container not found.");
        return;
    }

    const term = new Terminal({
        cursorBlink: true,
        theme: {
            background: '#000000',
            foreground: '#FFFFFF',
            cursor: '#FFFFFF',
        }
    });
    term.open(terminalContainer);

    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const wsUrl = `${protocol}//${window.location.hostname}:8080`;

    let sock;

    try {
        sock = new WebSocket(wsUrl);
    } catch (e) {
        term.write('Error creating WebSocket: ' + e);
        return;
    }


    sock.addEventListener('open', () => {
        term.write('Welcome to ownShell SSH Terminal\r\n');
        term.write('WebSocket connection established.\r\n');
    });

    sock.addEventListener('message', (event) => {
        term.write(event.data);
    });

    sock.addEventListener('close', (event) => {
        term.write('\r\nWebSocket connection closed.');
        if (event.reason) {
            term.write(` Reason: ${event.reason}`);
        }
    });

    sock.addEventListener('error', (event) => {
        term.write('\r\nWebSocket error.');
    });

    term.onData((data) => {
        if (sock.readyState === WebSocket.OPEN) {
            sock.send(data);
        }
    });

    // Fit terminal on window resize
    window.addEventListener('resize', () => {
        // This is a simplified fit. For a better experience,
        // an addon like xterm-addon-fit would be used.
        const cols = Math.floor(terminalContainer.clientWidth / 9);
        const rows = Math.floor(terminalContainer.clientHeight / 17);
        term.resize(cols, rows);
    });

    // Initial fit
    const cols = Math.floor(terminalContainer.clientWidth / 9);
    const rows = Math.floor(terminalContainer.clientHeight / 17);
    term.resize(cols, rows);
});
