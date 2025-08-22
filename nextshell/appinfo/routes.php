<?php
return [
    'routes' => [
        [
            'name' => 'page#index',
            'url' => '/',
            'verb' => 'GET'
        ],
        [
            'name' => 'settings_api#startDaemon',
            'url' => '/api/start',
            'verb' => 'POST'
        ],
        [
            'name' => 'settings_api#stopDaemon',
            'url' => '/api/stop',
            'verb' => 'POST'
        ],
        [
            'name' => 'settings_api#getStatus',
            'url' => '/api/status',
            'verb' => 'GET'
        ],
        [
            'name' => 'settings_api#saveSettings',
            'url' => '/api/settings',
            'verb' => 'PUT'
        ],
    ]
];
