<?php
/** @var \OCP\IL10N $l */
/** @var array $_ */
script('nextshell', 'admin');
style('nextshell', 'admin');
?>

<div id="nextshell-settings" class="section">
    <h2><?php p($l->t('NextShell Daemon Control')); ?></h2>
    <p><?php p($l->t('Manage the WebSocket daemon process. The daemon is required for the terminal to function.')); ?></p>
    <div id="daemon-status-container">
        <span><?php p($l->t('Daemon Status:')); ?></span>
        <span id="daemon-status" class="status-indicator"></span>
    </div>
    <button id="start-daemon" class="button"><?php p($l->t('Start Daemon')); ?></button>
    <button id="stop-daemon" class="button"><?php p($l->t('Stop Daemon')); ?></button>
    <div id="daemon-message" class="hidden"></div>

    <h2 style="margin-top: 2em;"><?php p($l->t('Connection Settings')); ?></h2>
    <p class="settings-hint"><?php p($l->t('Configure the WebSocket server and session behavior.')); ?></p>

    <table class="settings-table">
        <tr>
            <td><label for="websocket_port"><?php p($l->t('WebSocket Port')); ?></label></td>
            <td><input type="number" id="websocket_port" value="<?php p($_['websocket_port']); ?>" min="1025" max="65535"></td>
            <td class="settings-hint"><?php p($l->t('The port the WebSocket daemon will listen on. Must not be in use.')); ?></td>
        </tr>
        <tr>
            <td><label for="session_timeout"><?php p($l->t('Max Session Duration (seconds)')); ?></label></td>
            <td><input type="number" id="session_timeout" value="<?php p($_['session_timeout']); ?>" min="0"></td>
            <td class="settings-hint"><?php p($l->t('Maximum lifetime of a terminal session. 0 for unlimited.')); ?></td>
        </tr>
        <tr>
            <td><label for="idle_timeout"><?php p($l->t('Idle Timeout (seconds)')); ?></label></td>
            <td><input type="number" id="idle_timeout" value="<?php p($_['idle_timeout']); ?>" min="0"></td>
            <td class="settings-hint"><?php p($l->t('Time before an inactive session is automatically closed. 0 for unlimited.')); ?></td>
        </tr>
    </table>

    <h2 style="margin-top: 2em;"><?php p($l->t('Proxy Settings (Optional)')); ?></h2>
    <p class="settings-hint"><?php p($l->t('Configure an outbound proxy for SSH connections if your server requires one.')); ?></p>

    <table class="settings-table">
        <tr>
            <td><label for="proxy_type"><?php p($l->t('Proxy Type')); ?></label></td>
            <td>
                <select id="proxy_type">
                    <option value="" <?php if ($_['proxy_type'] === '') p('selected'); ?>><?php p($l->t('None')); ?></option>
                    <option value="http" <?php if ($_['proxy_type'] === 'http') p('selected'); ?>><?php p($l->t('HTTP')); ?></option>
                    <option value="socks5" <?php if ($_['proxy_type'] === 'socks5') p('selected'); ?>><?php p($l->t('SOCKS5')); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="proxy_host"><?php p($l->t('Proxy Host')); ?></label></td>
            <td><input type="text" id="proxy_host" value="<?php p($_['proxy_host']); ?>"></td>
        </tr>
        <tr>
            <td><label for="proxy_port"><?php p($l->t('Proxy Port')); ?></label></td>
            <td><input type="number" id="proxy_port" value="<?php p($_['proxy_port']); ?>" min="1" max="65535"></td>
        </tr>
        <tr>
            <td><label for="proxy_user"><?php p($l->t('Proxy Username')); ?></label></td>
            <td><input type="text" id="proxy_user" value="<?php p($_['proxy_user']); ?>"></td>
        </tr>
        <tr>
            <td><label for="proxy_password"><?php p($l->t('Proxy Password')); ?></label></td>
            <td><input type="password" id="proxy_password" value="<?php p($_['proxy_password']); ?>"></td>
        </tr>
    </table>

    <button id="save-settings" class="button primary"><?php p($l->t('Save settings')); ?></button>
    <div id="save-message" class="hidden"></div>
</div>
