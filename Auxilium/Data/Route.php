<?php

namespace Auxilium\Data;

use Auxilium\Support\Validator;
use Exception;
use Auxilium\Support\ArrayHelper as Arr;

class Route
{
    private string $route;
    private string $controller;
    private string $method;
    private array $permissions;
    private const ROUTE = 'ROUTE';
    private const CONTROLLER = 'CONTROLLER';
    private const PERMISSIONS = 'PERMISSIONS';

    public function __construct(array $params)
    {
        $this->route = Arr::get($params, self::ROUTE);
        $this->controller = Arr::get($params, self::CONTROLLER);
        $this->permissions = Arr::get($params, self::PERMISSIONS);
    }

    public function hasAccess(): bool
    {
        return true;
    }

    public function getController()
    {
        try {
            return new $this->controller(new Validator());
        } catch (Exception $exception) {
            throw new Exception('CONTROLLER NOT FOUND');
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}