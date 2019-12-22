<?php
use Monolog\Logger;

return [
    'fob.app_log' => [
        // BEAR default application context
        'app' => [
            'name' => 'my_project',
            'max_files' => 30,
            'level' => Logger::DEBUG,
            // monolog channel annotation name
            'app_logger' => [
                'suffix_format' => 'Ymd',
                'filename' => 'my_project.log',
            ],
        ],
        'prod' => [
            'level' => Logger::INFO,
        ],
    ]
];
