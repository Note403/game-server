<?php

namespace Auxilium;

class AutoLoader
{
    public static function load(string $namespace): void
    {
        include_once self::buildPath($namespace);
    }

    private static function buildPath(string $namespace): string
    {
        $namespace_parts = explode('\\', $namespace);
        $path = '';

        foreach ($namespace_parts as $index => $namespace_part) {
            if ($index != count($namespace_parts) - 1) {
                $path .= $namespace_part . '\\';
            } else {
                $path .= $namespace_part . '.php';
            }
        }

        return $path;
    }
}