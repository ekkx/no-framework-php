<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use PDO;

class UserRepository extends SQLiteRepository
{
    private string $table = "users";

    public function create(string $username, string $email, string $password): ?User
    {
        $sql = "INSERT INTO $this->table (username, email, password) VALUES (?, ?, ?)";

        $statement = $this::pdo()->prepare($sql);
        $statement->execute([$username, $email, $password]);

        return $this->findOneBy("email", $email);
    }

    /** @return User[] */
    public function findAll(): array
    {
        $sql = "SELECT * FROM $this->table";

        $statement = $this::pdo()->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findOneBy(string $col, mixed $value): ?User
    {
        $sql = "SELECT * FROM $this->table WHERE $col = ?";

        $statement = $this::pdo()->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute([$value]);

        return $statement->fetch() ?: null;
    }

    public function updateLastLoginAt(int $id): void
    {
        $sql = "UPDATE $this->table SET lastLoginAt = datetime('now') WHERE id = ?";

        $statement = $this::pdo()->prepare($sql);
        $statement->execute([$id]);
    }
}
