<?php

session_start();
ini_set('display_errors', '1');

include 'Auxilium\AutoLoader.php';
include 'Auxilium\App\Router.php';

spl_autoload_register(function (string $namespace) {
    \Auxilium\AutoLoader::load($namespace);
});

try {
    (new \Auxilium\App\Router())->execute();
} catch (Exception $exception) {
    \GameServer\Auxilium\Support\Response::error($exception->getMessage());
}