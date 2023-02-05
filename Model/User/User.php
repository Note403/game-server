<?php

namespace Model\User;

use Auxilium\Data\Model\Model;
use GameServer\Auxilium\Database\QueryBuilder;

class User extends Model
{
    public const ID = 'id';
    public const USERNAME = 'username';
    public const PASSWORD = 'password';
    public const EMAIL = 'email';
    public const ROLE = 'role';
    public const BLOCKED = 'blocked';

    public static string $table = 'users';
    public static array $fillable = [
        self::ID,
        self::USERNAME,
        self::PASSWORD,
        self::EMAIL,
        self::ROLE,
        self::BLOCKED,
    ];

    public static function hashPassword(string $password): string
    {
        return ($salt = self::createSalt()) . '$' . crypt($password, $salt);
    }

    private static function createSalt(): string
    {
        return str_split(md5(random_bytes(random_int(1000, 1000000)) . random_bytes(random_int(1000, 1000000))), 8)[0];
    }
}