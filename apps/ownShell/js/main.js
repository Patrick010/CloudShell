(function(document, $) {
    $(document).ready(function() {
        var term = new Terminal();
        var fitAddon = new FitAddon.FitAddon();
        term.loadAddon(fitAddon);
        term.open(document.getElementById('terminal'));
        fitAddon.fit();

        var ws;
        var hostTokens = JSON.parse($('#host-tokens').val());
        var connectButton = $('#connect-button');
        var hostSelect = $('#host-select');
        var statusSpan = $('#connection-status');

        connectButton.on('click', function() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.close();
                return;
            }

            var selectedHost = hostSelect.val();
            if (!selectedHost) {
                alert('Please select a host.');
                return;
            }

            var token = hostTokens[selectedHost];
            if (!token) {
                alert('Authentication token not found for selected host.');
                return;
            }

            // Construct WebSocket URL
            var protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            var wsUrl = protocol + '//' + window.location.host + OC.appswebroots.ownshell + '/ws/';

            statusSpan.text('Connecting...');
            connectButton.text('Disconnect');
            hostSelect.prop('disabled', true);

            ws = new WebSocket(wsUrl);

            ws.onopen = function() {
                statusSpan.text('Connected');
                // First message must be the JWT
                ws.send(token);
                term.focus();
                fitAddon.fit();
            };

            ws.onmessage = function(event) {
                // Write data from the WebSocket to the terminal
                term.write(event.data);
            };

            ws.onclose = function() {
                statusSpan.text('Disconnected');
                connectButton.text('Connect');
                hostSelect.prop('disabled', false);
                term.write('\n\r--- CONNECTION CLOSED ---\n\r');
            };

            ws.onerror = function() {
                statusSpan.text('Error');
                connectButton.text('Connect');
                hostSelect.prop('disabled', false);
                term.write('\n\r--- CONNECTION ERROR ---\n\r');
            };

            // Pipe data from the terminal to the WebSocket
            term.onData(function(data) {
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(data);
                }
            });
        });

        $(window).on('resize', function() {
            fitAddon.fit();
        });
    });
})(document, jQuery);
