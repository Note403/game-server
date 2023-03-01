<?php

namespace Auxilium\Support;

class App
{
    private const START_DIR = 'Auxilium';

    public static function cfgPath(): string
    {
        $pathArr = explode('\\', __DIR__);
        $path = '';

        foreach ($pathArr as $pathPart) {
            $path .= $pathPart . '\\';

            if ($pathPart == self::START_DIR)
                return $path . 'Config\\';
        }
    }

    public static function uuid(): string
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}