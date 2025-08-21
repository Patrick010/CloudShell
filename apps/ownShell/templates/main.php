<?php
script('ownshell', 'xterm');
script('ownshell', 'xterm-addon-fit');
script('ownshell', 'main');
style('ownshell', 'xterm');
style('ownshell', 'main');
?>

<div id="app-content">
    <div id="ownshell-controls">
        <label for="host-select">Select Host:</label>
        <select id="host-select">
            <?php if (empty($_['hosts'])): ?>
                <option disabled selected>No hosts configured</option>
            <?php else: ?>
                <?php foreach ($_['hosts'] as $host): ?>
                    <option value="<?php p($host); ?>"><?php p($host); ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <button id="connect-button" <?php if (empty($_['hosts'])) p('disabled'); ?>>Connect</button>
        <span id="connection-status"></span>
    </div>
    <div id="terminal"></div>
</div>

<!-- Pass the host-to-token map to the JavaScript -->
<input type="hidden" id="host-tokens" value="<?php p(json_encode($_['tokens'])); ?>">
