<?php

namespace Auxilium\App;

use Auxilium\Data\Request;
use Auxilium\Data\Route;
use Exception;
use Auxilium\Support\ArrayHelper as Arr;
use Auxilium\Support\Config;

class Router
{
    private Request $request;

    public function __construct()
    {
        $request_data = json_decode(file_get_contents('php://input'), true);

        if (isset($_GET)) {
            $request_data = $request_data == null
                ? $_GET
                : Arr::combine([$request_data, $_GET]);
        }

        $this->request = new Request(
            $_SERVER['REQUEST_URI'],
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['REQUEST_TIME'],
            $request_data
        );
    }

    public function execute()
    {
        $this->request->URI = explode('?', $this->request->URI)[0];
        $route_parts = explode('/', $this->request->URI);

        unset($route_parts[0]);
        unset($route_parts[1]);

        $route = $this->getRoute(implode('\\', $route_parts));

        if (!$route)
            throw new Exception('INVALID ROUTE');

        ($route->getController())($this->request);
    }

    private function getRoute(string $request_route): Route|bool
    {
        $routes = Config::get('routes');

        foreach ($routes as $route) {
            if ($route['ROUTE'] == $request_route)
                return new Route($route);
        }

        return false;
    }
}