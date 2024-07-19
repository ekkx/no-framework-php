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

        return $this->findOneByEmail($email);
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

    public function findOneByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM $this->table WHERE email = ?";

        $statement = $this::pdo()->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $statement->execute([$email]);

        return $statement->fetch() ?: null;
    }
}
