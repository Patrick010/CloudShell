<?php
/** @var array $_ */
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" />
<script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.min.js"></script>
<?php
style('nextshell', 'style');
script('nextshell', 'terminal');

// Pass parameters to the frontend
$params = [
    'websocketUrl' => $_['websocket_url'],
];
?>
<div id="terminal-container"></div>
<script id="nextshell-params" type="application/json"><?php print_unescaped(json_encode($params)); ?></script>
