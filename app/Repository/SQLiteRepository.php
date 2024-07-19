<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class SQLiteRepository
{
    private static PDO $pdo;

    protected static function pdo(): PDO
    {
        if (!isset(self::$pdo)) {
            self::$pdo = new PDO("sqlite:" . __DIR__ . "/../../database/database.sqlite");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
