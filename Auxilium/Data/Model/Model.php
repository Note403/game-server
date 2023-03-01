<?php

namespace Auxilium\Data\Model;

use Auxilium\Database\QueryBuilder;
use Exception;

class Model
{
    public static string $table;
    public static array $fillable;

    /**
     * @throws Exception
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(self::$table, self::$fillable);
    }
}