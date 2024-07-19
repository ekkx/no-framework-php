<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\Model;

class User extends Model
{
    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;
}
