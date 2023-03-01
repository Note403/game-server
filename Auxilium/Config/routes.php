<?php

return [
    [
        'ROUTE' => 'login',
        'CONTROLLER' => \Controller\User\LoginController::class,
        'PERMISSIONS' => [],
    ],
    [
        'ROUTE' => 'user\create',
        'CONTROLLER' => \Controller\User\CreateUserController::class,
        'PERMISSIONS' => [],
    ]
];