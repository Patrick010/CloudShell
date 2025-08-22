(function($, OC) {
    $(document).ready(function() {
        const startBtn = $('#start-daemon');
        const stopBtn = $('#stop-daemon');
        const saveBtn = $('#save-settings');
        const statusIndicator = $('#daemon-status');
        const messageDiv = $('#daemon-message');
        const saveMessageDiv = $('#save-message');

        const API_BASE_URL = OC.generateUrl('apps/nextshell/api');

        function showMessage(element, message, isError = false) {
            element.text(message).toggleClass('success', !isError).toggleClass('error', isError).removeClass('hidden');
            setTimeout(() => element.addClass('hidden'), 5000);
        }

        function updateStatus() {
            statusIndicator.text('Checking...');
            $.ajax({
                url: `${API_BASE_URL}/status`,
                type: 'GET',
                success: function(response) {
                    if (response.running) {
                        statusIndicator.text('Running').css('color', 'green');
                        startBtn.prop('disabled', true);
                        stopBtn.prop('disabled', false);
                    } else {
                        statusIndicator.text('Stopped').css('color', 'red');
                        startBtn.prop('disabled', false);
                        stopBtn.prop('disabled', true);
                    }
                },
                error: function() {
                    statusIndicator.text('Error').css('color', 'red');
                    showMessage(messageDiv, 'Failed to get daemon status.', true);
                }
            });
        }

        startBtn.on('click', function() {
            $(this).prop('disabled', true);
            $.ajax({
                url: `${API_BASE_URL}/start`,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showMessage(messageDiv, 'Daemon started successfully.', false);
                    } else {
                        showMessage(messageDiv, `Failed to start daemon: ${response.message}`, true);
                    }
                    updateStatus();
                },
                error: function(xhr) {
                    showMessage(messageDiv, `Error starting daemon: ${xhr.responseJSON?.message || 'Unknown error'}`, true);
                    updateStatus();
                }
            });
        });

        stopBtn.on('click', function() {
            $(this).prop('disabled', true);
            $.ajax({
                url: `${API_BASE_URL}/stop`,
                type: 'POST',
                success: function(response) {
                     if (response.success) {
                        showMessage(messageDiv, 'Daemon stopped successfully.', false);
                    } else {
                        showMessage(messageDiv, `Failed to stop daemon: ${response.message}`, true);
                    }
                    updateStatus();
                },
                error: function() {
                    showMessage(messageDiv, 'Error stopping daemon.', true);
                    updateStatus();
                }
            });
        });

        saveBtn.on('click', function() {
            const settings = {
                websocket_port: $('#websocket_port').val(),
                session_timeout: $('#session_timeout').val(),
                idle_timeout: $('#idle_timeout').val(),
                proxy_type: $('#proxy_type').val(),
                proxy_host: $('#proxy_host').val(),
                proxy_port: $('#proxy_port').val(),
                proxy_user: $('#proxy_user').val(),
                proxy_password: $('#proxy_password').val()
            };

            $.ajax({
                url: `${API_BASE_URL}/settings`, // This route needs to be created
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(settings),
                success: function(response) {
                    showMessage(saveMessageDiv, 'Settings saved successfully.', false);
                },
                error: function() {
                    showMessage(saveMessageDiv, 'Failed to save settings.', true);
                }
            });
        });

        // Initial status check
        updateStatus();
    });
})(jQuery, OC);
