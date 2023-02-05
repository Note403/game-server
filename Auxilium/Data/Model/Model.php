<?php

namespace Auxilium\Data\Model;

use GameServer\Auxilium\Database\QueryBuilder;

class Model
{
    public static string $table;
    public static array $fillable;

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(self::$table, self::$fillable);
    }
}