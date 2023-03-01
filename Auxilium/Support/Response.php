<?php

namespace Auxilium\Support;

class Response
{
    private static array $response = [
        'success' => true,
        'error' => null,
        'data' => null,
    ];

    public static function staticConstructor()
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function json(array $data): void
    {
        self::$response['data'] = $data;

        echo json_encode(self::$response);
    }

    public static function success(): void
    {
        echo json_encode(self::$response);
    }

    public static function error(string $error): void
    {
        self::$response['success'] = false;
        self::$response['error'] = $error;

        echo json_encode(self::$response);
    }
}

Response::staticConstructor();