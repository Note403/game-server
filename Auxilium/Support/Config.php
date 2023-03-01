<?php

namespace Auxilium\Support;

use Exception;
use Auxilium\Support\ArrayHelper as Arr;

class Config
{
    public static function get(string $keys)
    {
        if ($keys == null)
            return null;

        if (!str_contains('.', $keys)) {
            $cfgPath = App::cfgPath() . $keys . '.php';

            if (!file_exists($cfgPath))
                return null;

            try {
                return include $cfgPath;
            } catch (Exception $exception) {
                return null;
            }
        } else {
            $keyArr = explode('.', $keys);
            $cfgPath = App::cfgPath() . Arr::pull($keyArr, 0) . '.php';

            if (!file_exists($cfgPath))
                return null;

            try {
                $configData = include $cfgPath;
            } catch (Exception $exception) {
                return null;
            }

            if ($configData == null)
                return null;

            return Arr::get($keyArr, implode('.', $keyArr));
        }
    }

    public static function dbData()
    {
        return include App::cfgPath() . 'db.php';
    }
}