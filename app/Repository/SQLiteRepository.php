<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class SQLiteRepository
{
    // The PDO instance used for database operations.
    // This follows the Singleton pattern to ensure only one PDO instance is used throughout the application.
    private static PDO $pdo;

    /**
     * Returns the PDO instance for database operations.
     *
     * @return PDO
     */
    protected static function pdo(): PDO
    {
        if (!isset(self::$pdo)) {
            // Initialize a new PDO instance with the SQLite database file if the PDO instance is not set.
            // This is where the Singleton pattern is implemented.
            self::$pdo = new PDO("sqlite:" . __DIR__ . "/../../database/database.sqlite");

            // Set the PDO error mode to exception.
            // This means that if there is an error, the PDO will throw an exception.
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
