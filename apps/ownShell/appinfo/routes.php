<?php
return [
    'routes' => [
        // Page routes
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

        // Admin settings routes
        ['name' => 'admin#save', 'url' => '/settings/admin', 'verb' => 'POST'],
    ]
];
