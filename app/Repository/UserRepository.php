<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use PDO;

class UserRepository extends SQLiteRepository
{
    private string $table = "users"; // The name of the table in the SQLite database.

    /**
     * Creates a new user in the database.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return ?User
     */
    public function create(string $username, string $email, string $password): ?User
    {
        $sql = "INSERT INTO $this->table (username, email, password) VALUES (?, ?, ?)";

        $statement = $this::pdo()->prepare($sql);
        $statement->execute([$username, $email, $password]);

        return $this->findOneBy("email", $email);
    }

    /**
     * Returns an array of all User objects in the database.
     *
     * @return User[]
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM $this->table";

        $statement = $this::pdo()->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Finds a user in the database by a specific column and value.
     *
     * @param string $col
     * @param mixed $value
     *
     * @return ?User
     */
    public function findOneBy(string $col, mixed $value): ?User
    {
        $sql = "SELECT * FROM $this->table WHERE $col = ?";

        $statement = $this::pdo()->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute([$value]);

        return $statement->fetch() ?: null;
    }

    /**
     * Updates the last login time of a user.
     *
     * @param int $id
     */
    public function updateLastLoginAt(int $id): void
    {
        $sql = "UPDATE $this->table SET lastLoginAt = datetime('now') WHERE id = ?";

        $statement = $this::pdo()->prepare($sql);
        $statement->execute([$id]);
    }
}
