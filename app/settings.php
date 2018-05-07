<?php
declare(strict_types=1);

if (getenv('APP_DEBUG')) {
    $debug = true;
} else {
    $debug = true;
}
return [
    // Slim Settings
    'displayErrorDetails' => $debug,
    'determineRouteBeforeAppMiddleware' => true,

    // Database settings
    'db' => [
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'charset' => getenv('DB_CHARSET'),
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pass' => getenv('DB_PASS'),
    ],

    'mail' => [
        'username' => getenv('EMAIL_USERNAME'),
        'password' => getenv('EMAIL_PASSWORD'),
        'host' => getenv('EMAIL_HOST'),
        'smtpSecureType' => 'tls',
        'port' => '587',
    ],

    // View settings
    'view' => [
        'template_path' => __DIR__ . '/view',
        'twig' => [
            'auto_reload' => true,
            'cache' => __DIR__ . '/../cache/twig',
            'debug' => $debug,
        ],
    ],

    // Monolog settings
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../logs/app.' . date('Y-m-d') . '.log'
    ],

    'version' => [
        'project' => getenv('PROJECT'),
        'version' => getenv('VERSION'),
        'stage' => getenv('STAGE'),
        'date' => getenv('DATE'),
        'branch' => getenv('BRANCH'),
        'repository' => getenv('REPOSITORY'),
        'repository_link' => getenv('REPOSITORY_LINK')
    ]
];
