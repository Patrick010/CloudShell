(function($, OC) {
    $(document).ready(function() {
        const params = JSON.parse($('#nextshell-params').text());
        const term = new Terminal({
            cursorBlink: true,
            convertEol: true,
            fontFamily: `'Fira Mono', monospace`,
            fontSize: 14,
        });
        const fitAddon = new FitAddon.FitAddon();
        term.loadAddon(fitAddon);
        term.open(document.getElementById('terminal-container'));
        fitAddon.fit();

        $(window).on('resize', function() {
            fitAddon.fit();
        });

        let ws;
        let state = 'disconnected'; // disconnected, connecting, await_password, connected
        let lineBuffer = '';

        function connect() {
            ws = new WebSocket(params.websocketUrl);

            ws.onopen = function() {
                state = 'connecting';
                term.writeln('Welcome to NextShell!');
                term.write('Connect to (user@host): ');
            };

            ws.onmessage = function(event) {
                const msg = JSON.parse(event.data);
                switch (msg.type) {
                    case 'status':
                        term.writeln(`\r\n${msg.message}`);
                        break;
                    case 'prompt_password':
                        state = 'await_password';
                        term.write('\r\nPassword: ');
                        break;
                    case 'auth_success':
                        state = 'connected';
                        term.writeln('\r\nAuthentication successful.');
                        break;
                    case 'data':
                        term.write(msg.payload);
                        break;
                    case 'error':
                        term.writeln(`\r\n\x1b[31mError: ${msg.message}\x1b[0m`);
                        ws.close();
                        break;
                }
            };

            ws.onclose = function() {
                state = 'disconnected';
                term.writeln('\r\n\x1b[31mWebSocket connection closed.\x1b[0m');
            };

            ws.onerror = function(err) {
                state = 'disconnected';
                term.writeln(`\r\n\x1b[31mWebSocket error observed: ${err}\x1b[0m`);
            };
        }

        term.onKey(({ key, domEvent }) => {
            if (state === 'disconnected' || state === 'connected') {
                if (domEvent.keyCode === 13) { // Enter
                    if (state === 'connected') {
                         ws.send(JSON.stringify({ type: 'data', payload: '\r' }));
                    }
                    lineBuffer = '';
                } else if (domEvent.keyCode === 8) { // Backspace
                    if (lineBuffer.length > 0) {
                        term.write('\b \b');
                        lineBuffer = lineBuffer.slice(0, -1);
                    }
                } else {
                    term.write(key);
                    lineBuffer += key;
                    if (state === 'connected') {
                        ws.send(JSON.stringify({ type: 'data', payload: key }));
                    }
                }
            } else if (state === 'connecting') {
                 if (domEvent.keyCode === 13) { // Enter
                    term.write('\r\n');
                    ws.send(JSON.stringify({ type: 'connect', target: lineBuffer }));
                    lineBuffer = '';
                } else if (domEvent.keyCode === 8) { // Backspace
                     if (lineBuffer.length > 0) {
                        term.write('\b \b');
                        lineBuffer = lineBuffer.slice(0, -1);
                    }
                } else {
                    term.write(key);
                    lineBuffer += key;
                }
            } else if (state === 'await_password') {
                if (domEvent.keyCode === 13) { // Enter
                    ws.send(JSON.stringify({ type: 'auth', password: lineBuffer }));
                    lineBuffer = '';
                } else if (domEvent.keyCode === 8) { // Backspace
                     if (lineBuffer.length > 0) {
                        // Do not echo backspace for password
                        lineBuffer = lineBuffer.slice(0, -1);
                    }
                } else {
                    // Do not echo password characters
                    lineBuffer += key;
                }
            }
        });

        term.onResize(({ cols, rows }) => {
            if (state === 'connected') {
                ws.send(JSON.stringify({ type: 'resize', cols, rows }));
            }
        });

        // Start the connection process
        connect();
    });
})(jQuery, OC);
